<?php

namespace Document\Service;

use Contractor\Entity\ContractorCompany;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Document\Domain\SaleContractEntity;
use Document\Domain\SaleWagonEntity;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Application\Service;
use Document\InputFilter;
use Document\Service\Rate\AdapterFactory;
use Document\Service\Rate\MixedAdapter;
use Document\Service\Repository\SaleContractDb;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Service\WarehouseLogManager;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetSharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Transport\Domain\CarrierEntity;
use Transport\Domain\RateValueEntity;
use Transport\Service\RateManager;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class SaleWagonManager {

    /**
     * @var SaleContractDb
     */
    protected $saleContractDbRepository;

    /**
     * @var  Repository\SaleWagonDb
     */
    protected $saleWagonDbRepository;

    /**
     * @var InputFilter\SaleWagon
     */
    protected $saleWagonInputFilter;

    /**
     * @var FinanceManager
     */
    protected $financeManager;

    /**
     * @var RateManager
     */
    protected $rateManager;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var ContractorPlantManager
     */
    protected $plantManager;

    /**
     * SaleWagonManager constructor.
     * @param SaleContractDb $saleContractDbRepository
     * @param ContractorCompanyManager $companyManager
     * @param ContractorPlantManager $plantManager
     * @param InputFilter\SaleWagon $saleWagonInputFilter
     * @param Repository\SaleWagonDb $saleWagonDbRepository
     * @param FinanceManager $financeManager
     * @param RateManager $rateManager
     * @param WarehouseLogManager $warehouseLogManager
     */
    public function __construct(SaleContractDb $saleContractDbRepository,
                                ContractorCompanyManager $companyManager,
                                ContractorPlantManager $plantManager,
                                InputFilter\SaleWagon $saleWagonInputFilter,
                                Repository\SaleWagonDb $saleWagonDbRepository,
                                FinanceManager $financeManager,
                                RateManager $rateManager,
                                WarehouseLogManager $warehouseLogManager) {
        $this->saleContractDbRepository = $saleContractDbRepository;
        $this->companyManager = $companyManager;
        $this->plantManager = $plantManager;
        $this->saleWagonInputFilter = $saleWagonInputFilter;
        $this->saleWagonDbRepository = $saleWagonDbRepository;
        $this->financeManager = $financeManager;
        $this->rateManager = $rateManager;
        $this->warehouseLogManager = $warehouseLogManager;
    }

    /**
     * @param null $contractId
     * @param null $filterData
     * @return \Zend\Paginator\Paginator
     */
    public function getWagonsPaginator($contractId = null, $filterData = null) {
        $paginator = $this->saleWagonDbRepository->fetchWagonsPaginator($contractId, $filterData);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getWagonsValueOptions() {
        $columns = ['wagon_id', 'wagon_number'];
        $options = array_map(function ($saleWagon) {
            return [
                //'attributes' => [],
                'label' => $saleWagon['wagon_number'],
                'value' => $saleWagon['wagon_id'],
            ];
        }, $this->saleWagonDbRepository->fetchWagonsArray($columns));
        return $options;
    }

    /**
     * @param $wagonId
     * @return SaleWagonEntity
     */
    public function getWagonById($wagonId) {
        $wagonId = intval($wagonId);
        $saleWagon = $this->saleWagonDbRepository->fetchWagonById($wagonId);
        return $saleWagon;
    }

    /**
     * @param $rateId
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getWagonsByRateId($rateId) {
        return $this->saleWagonDbRepository->fetchWagonsByRateId($rateId);
    }

    /**
     * @param SaleWagonEntity $object
     * @return Service\Result
     * @throws \Contractor\Exception\ErrorException
     */
    public function loadWagon(SaleWagonEntity $object) {

        if (!count($wagons = $object->getWagons()))
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagon data was not saved.');
        try {


            // Начало транзакции загрузки вагона
            $this->saleContractDbRepository->beginTransaction();

            $contract = $this->saleContractDbRepository->fetchContractById($object->getContractId());

            // Рассчет стоимости доставки по умолчанию
            $transportRate = $this->rateManager->getRateById($object->getRateId());

            // Получение основной компании
            /** @var ContractorCompany $companyContractor */
            $companyContractor = $this->companyManager->getContractorById($contract->getCompanyId());

            // Получение компании завода
            /** @var ContractorPlant $plantContractor */
            $plantContractor = $this->plantManager->getContractorById($companyContractor->getPlantId());

            /** @var \ArrayObject $wagon */
            foreach ($wagons as $wagon) {

                $productPrice = $wagon->offsetGet('loading_weight') * $contract->getPrice(true);

                // Формирование обьекта одиночного вагона
                $currentWagon = clone $object;
                $currentWagon->setWagons(null);
                $currentWagon->setStatus($currentWagon::STATUS_LOADED);
                $currentWagon->setWagonNumber($wagon->offsetGet('wagon_number'));
                $currentWagon->setProductPrice($productPrice);
                $currentWagon->setLoadingWeight($wagon->offsetGet('loading_weight'));
                $currentWagon->setRateId($object->getRateId());
                $currentWagon->setRateValueId($wagon->offsetGet('rate_value_id'));

                // Рассчет стоимости доставки по умолчанию
                if (($rateId = $object->getRateId()) && ($rateValueId = $currentWagon->getRateValueId())) {

                    $transportRate = $this->rateManager->getRateById($rateId);
                    $transportRateValue = $this->rateManager->getRateValueById($rateValueId);

                    $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRateValue->getPrice(), $currentWagon->getLoadingWeight());

                    if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                        $rateAdapter->setMinWeight($minWeight);
                    }
                    $debtToCarrier = $rateAdapter->calculate($contract->getTax());
                } else {
                    $debtToCarrier = 0;
                }
                $object->setTransportPrice($debtToCarrier);

                $currentWagon = $this->saleWagonDbRepository->saveWagon($currentWagon);


                if ($contract::CONDITIONS_TYPE_FCA != $contract->getConditions()) {
                    // У завода создается задолженность за перевозку перед перевозчиком
                    $carrierTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CARRIER);
                    $carrierTransaction->setCompanyId($plantContractor->getContractorId());
                    $carrierTransaction->setContractorId($transportRate->getCarrierId());
                    $carrierTransaction->setContractType($carrierTransaction::CONTRACT_SALE);
                    $carrierTransaction->setCredit($debtToCarrier);
                    $carrierTransaction->setWagonId($currentWagon->getWagonId());
                    $carrierTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                    // У компании создается задолженность за перевозку перед заводом на ту же сумму (Компания должна заводу - минус в компанию)
                    $companyToPlantDebtTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_PLANT);
                    $companyToPlantDebtTransaction->setCompanyId($companyContractor->getContractorId());
                    $companyToPlantDebtTransaction->setContractorId($plantContractor->getContractorId());
                    $companyToPlantDebtTransaction->setContractType($companyToPlantDebtTransaction::CONTRACT_SALE);
                    $companyToPlantDebtTransaction->setCredit($debtToCarrier);
                    $companyToPlantDebtTransaction->setWagonId($currentWagon->getWagonId());
                    $companyToPlantDebtTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                    // У компании создается задолженность за перевозку перед заводом на ту же сумму (Компания должна заводу - плюс в завод)
                    $companyToPlantCreditTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_COMPANY);
                    $companyToPlantCreditTransaction->setCompanyId($plantContractor->getContractorId());
                    $companyToPlantCreditTransaction->setContractorId($companyContractor->getContractorId());
                    $companyToPlantCreditTransaction->setContractType($companyToPlantDebtTransaction::CONTRACT_SALE);
                    $companyToPlantCreditTransaction->setDebit($debtToCarrier);
                    $companyToPlantCreditTransaction->setWagonId($currentWagon->getWagonId());
                    $companyToPlantCreditTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                    // Формирование задолженности покупателя за перевозку
                    $customerTransportTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CUSTOMER);
                    $customerTransportTransaction->setCompanyId($companyContractor->getContractorId());
                    $customerTransportTransaction->setContractorId($contract->getCustomerId());
                    $customerTransportTransaction->setContractType($customerTransportTransaction::CONTRACT_SALE);
                    $customerTransportTransaction->setDebit($debtToCarrier);
                    $customerTransportTransaction->setWagonId($currentWagon->getWagonId());
                    $customerTransportTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));
                }

                // Формирование задолженности покупателя за товар
                $customerSaleTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CUSTOMER);
                $customerSaleTransaction->setCompanyId($companyContractor->getContractorId());
                $customerSaleTransaction->setContractorId($contract->getCustomerId());
                $customerSaleTransaction->setContractType($customerSaleTransaction::CONTRACT_SALE);
                $customerSaleTransaction->setDebit($productPrice);
                $customerSaleTransaction->setWagonId($currentWagon->getWagonId());
                $customerSaleTransaction->setComment(sprintf('Отгрузка продукции по договору %s за вагон %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                // Фиксирование задолженностей в зависимости от условий доставки
                switch ($contract->getConditions()) {
                    //switch ($wagon->offsetGet('conditions')) {
                    // Записываеться задолженность покупателя
                    case $contract::CONDITIONS_TYPE_FCA:
                        // Клиент должен компании за продукцию
                        $this->financeManager->saveTransaction($customerSaleTransaction);
                        break;
                    case $contract::CONDITIONS_TYPE_CPT:
                        // Клиент должен компании за продукцию
                        $this->financeManager->saveTransaction($customerSaleTransaction);
                        if (CarrierEntity::TYPE_TRAIN == $contract->getCarrierType()) {
                            if (isset($companyToPlantDebtTransaction)) {
                                // Компания должна заводу - минус компании
                                $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                            }
                            if (isset($companyToPlantCreditTransaction)) {
                                // Заводу должна компания - плюс заводу
                                $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                            }
                            if (isset($carrierTransaction)) {
                                // Завод должен перевозчику - минус заводу
                                $this->financeManager->saveTransaction($carrierTransaction);
                            }
                        } elseif (isset($carrierTransaction)) {
                            // Компания должна перевозчику - минус компании
                            $carrierTransaction->setCompanyId($companyContractor->getContractorId());
                            $carrierTransaction->setComment(sprintf('Авто. Перевозка продукции по договору %s за авто %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }
                        break;
                    // Записываеться задолженность перед покупателем за сырье и за перевозку
                    case $contract::CONDITIONS_TYPE_CPT_RETURN:
                        // Если случай CPT с возвратом средств, тогда возвратные средства выставляются компании, а компания заводу
                        // Клиент должен компании за продукцию
                        $this->financeManager->saveTransaction($customerSaleTransaction);
                        if (isset($customerTransportTransaction)) {
                            // Клиент должен компании за перевозку
                            $this->financeManager->saveTransaction($customerTransportTransaction);
                        }
                        if (CarrierEntity::TYPE_TRAIN == $contract->getCarrierType()) {
                            if (isset($companyToPlantDebtTransaction)) {
                                // Компания должна заводу - минус компании
                                $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                            }
                            if (isset($companyToPlantCreditTransaction)) {
                                // Заводу должна компания - плюс заводу
                                $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                            }
                            if (isset($carrierTransaction)) {
                                // Завод должен перевозчику - минус заводу
                                $this->financeManager->saveTransaction($carrierTransaction);
                            }
                        } elseif (isset($carrierTransaction)) {
                            // Компания должна перевозчику - минус компании
                            $carrierTransaction->setCompanyId($companyContractor->getContractorId());
                            $carrierTransaction->setComment(sprintf('Авто. Перевозка продукции по договору %s за авто %s', $contract->getContractNumber(), $currentWagon->getWagonNumber()));
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }

                        break;
                    default:
                        throw new Exception\InvalidArgumentException('Invalid delivery condition type');
                }

                $warehouseTransaction = new WarehouseLogEntity();
                $warehouseTransaction->setContractorId($contract->getCustomerId());
                $warehouseTransaction->setWarehouseId($contract->getWarehouseId());
                $warehouseTransaction->setResourceId($contract->getProductId());
                $warehouseTransaction->setWagonId($currentWagon->getWagonId());
                $warehouseTransaction->setResourceWeight($currentWagon->getLoadingWeight());
                $warehouseTransaction->setComment(sprintf('Отгрузка продукции по договору %s в вагон %s',
                    $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                $this->warehouseLogManager->output($warehouseTransaction);

            }
            // Фиксирование транзакции добавления вагон(а/ов)
            $this->saleContractDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения ErrorException
            $this->saleContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        } catch (Exception\InvalidArgumentException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения InvalidArgumentException
            $this->saleContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }


    /**
     * @param SaleWagonEntity $object
     * @return Service\Result
     * @throws \Contractor\Exception\ErrorException
     */
    public function saveWagon(SaleWagonEntity $object) {
        try {
            // Начало транзакции загрузки вагона
            $this->saleContractDbRepository->beginTransaction();

            $contract = $this->saleContractDbRepository->fetchContractById($object->getContractId());

            $transportRate = $this->rateManager->getRateById($object->getRateId());

            // Получение основной компании
            /** @var ContractorCompany $companyContractor */
            $companyContractor = $this->companyManager->getContractorById($contract->getCompanyId());

            // Получение компании завода
            /** @var ContractorPlant $plantContractor */
            $plantContractor = $this->plantManager->getContractorById($companyContractor->getPlantId());

            // Рассчет стоимости доставки по умолчанию
            if (($rateId = $object->getRateId()) && ($rateValueId = $object->getRateValueId())) {

                $transportRate = $this->rateManager->getRateById($rateId);
                $transportRateValue = $this->rateManager->getRateValueById($rateValueId);

                $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRateValue->getPrice(), $object->getLoadingWeight());

                if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                    $rateAdapter->setMinWeight($minWeight);
                }
                $debtToCarrier = $rateAdapter->calculate($contract->getTax());
            } else {
                $debtToCarrier = 0;
            }

            $object->setTransportPrice($debtToCarrier);

            //$productPrice = $object->getLoadingWeight() * $contract->getPrice(true);
            $productPrice = bcmul($contract->getPrice(true), $object->getLoadingWeight(), 2);

            $object->setProductPrice($productPrice);
            $object->setStatus($object::STATUS_LOADED);
            $object = $this->saleWagonDbRepository->saveWagon($object);

            // Удаление старых записей о задолженностях
            $this->financeManager->deleteTransactionByWagonId($object->getWagonId(), TransactionEntity::CONTRACT_SALE);
            // Удаление старых записей по складах
            $this->warehouseLogManager->deleteLogByWagonId($object->getWagonId());

            if ($contract::CONDITIONS_TYPE_FCA != $contract->getConditions()) {

                // У завода создается задолженность за перевозку перед перевозчиком
                $carrierTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CARRIER);
                $carrierTransaction->setCompanyId($plantContractor->getContractorId());
                $carrierTransaction->setContractorId($transportRate->getCarrierId());
                $carrierTransaction->setContractType($carrierTransaction::CONTRACT_SALE);
                $carrierTransaction->setCredit($debtToCarrier);
                $carrierTransaction->setWagonId($object->getWagonId());
                $carrierTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                $carrierTransaction->setCreated($object->getLoadingDate());

                // У компании создается задолженность за перевозку перед заводом на ту же сумму (Компания должна заводу - минус в компанию)
                $companyToPlantDebtTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_PLANT);
                $companyToPlantDebtTransaction->setCompanyId($companyContractor->getContractorId());
                $companyToPlantDebtTransaction->setContractorId($plantContractor->getContractorId());
                $companyToPlantDebtTransaction->setContractType($companyToPlantDebtTransaction::CONTRACT_SALE);
                $companyToPlantDebtTransaction->setCredit($debtToCarrier);
                $companyToPlantDebtTransaction->setWagonId($object->getWagonId());
                $companyToPlantDebtTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                $companyToPlantDebtTransaction->setCreated($object->getLoadingDate());

                // У компании создается задолженность за перевозку перед заводом на ту же сумму (Компания должна заводу - плюс в завод)
                $companyToPlantCreditTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_COMPANY);
                $companyToPlantCreditTransaction->setCompanyId($plantContractor->getContractorId());
                $companyToPlantCreditTransaction->setContractorId($companyContractor->getContractorId());
                $companyToPlantCreditTransaction->setContractType($companyToPlantDebtTransaction::CONTRACT_SALE);
                $companyToPlantCreditTransaction->setDebit($debtToCarrier);
                $companyToPlantCreditTransaction->setWagonId($object->getWagonId());
                $companyToPlantCreditTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                $companyToPlantCreditTransaction->setCreated($object->getLoadingDate());

            }

            // Формирование задолженности покупателя за товар
            $customerSaleTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CUSTOMER);
            $customerSaleTransaction->setCompanyId($companyContractor->getContractorId());
            $customerSaleTransaction->setContractorId($contract->getCustomerId());
            $customerSaleTransaction->setContractType($customerSaleTransaction::CONTRACT_SALE);
            $customerSaleTransaction->setDebit($productPrice);
            $customerSaleTransaction->setWagonId($object->getWagonId());
            $customerSaleTransaction->setComment(sprintf('Отгрузка продукции по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
            $customerSaleTransaction->setCreated($object->getLoadingDate());

            // Формирование задолженности покупателя за перевозку
            $customerTransportTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CUSTOMER);
            $customerTransportTransaction->setCompanyId($companyContractor->getContractorId());
            $customerTransportTransaction->setContractorId($contract->getCustomerId());
            $customerTransportTransaction->setContractType($customerTransportTransaction::CONTRACT_SALE);
            $customerTransportTransaction->setDebit($debtToCarrier);
            $customerTransportTransaction->setWagonId($object->getWagonId());
            $customerTransportTransaction->setComment(sprintf('Перевозка продукции по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
            $customerTransportTransaction->setCreated($object->getLoadingDate());

            // Фиксирование задолженностей в зависимости от условий доставки
            switch ($contract->getConditions()) {
                //switch ($wagon->offsetGet('conditions')) {
                // Записываеться задолженность покупателя
                case $contract::CONDITIONS_TYPE_FCA:
                    // Клиент должен компании за продукцию
                    $this->financeManager->saveTransaction($customerSaleTransaction);
                    break;
                case $contract::CONDITIONS_TYPE_CPT:
                    // Клиент должен компании за продукцию
                    $this->financeManager->saveTransaction($customerSaleTransaction);
                    /*
                    // Компания должна заводу - минус компании
                    $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                    // Заводу должна компания - плюс заводу
                    $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                    // Завод должен перевозчику - минус заводу
                    $this->financeManager->saveTransaction($carrierTransaction);
                    */

                    if (isset($carrierTransaction)) {
                        if (CarrierEntity::TYPE_TRAIN == $contract->getCarrierType()) {
                            if (isset($companyToPlantDebtTransaction)) {
                                // Компания должна заводу - минус компании
                                $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                            }
                            if (isset($companyToPlantCreditTransaction)) {
                                // Заводу должна компания - плюс заводу
                                $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                            }
                            // Завод должен перевозчику - минус заводу
                            $this->financeManager->saveTransaction($carrierTransaction);
                        } else {
                            // Компания должна перевозчику - минус компании
                            $carrierTransaction->setCompanyId($companyContractor->getContractorId());
                            $carrierTransaction->setComment(sprintf('Авто. Перевозка продукции по договору %s за авто %s', $contract->getContractNumber(), $object->getWagonNumber()));
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }
                    }
                    break;
                // Записываеться задолженность перед покупателем за сырье и за перевозку
                case $contract::CONDITIONS_TYPE_CPT_RETURN:
                    // Если случай CPT с возвратом средств, тогда возвратные средства выставляются компании, а компания заводу
                    // Клиент должен компании за продукцию
                    $this->financeManager->saveTransaction($customerSaleTransaction);
                    // Клиент должен компании за перевозку
                    $this->financeManager->saveTransaction($customerTransportTransaction);
                    /*
                    // Компания должна заводу - минус компании
                    $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                    // Заводу должна компания - плюс заводу
                    $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                    // Завод должен перевозчику - минус заводу
                    $this->financeManager->saveTransaction($carrierTransaction);
                    */
                    if (isset($carrierTransaction)) {
                        if (CarrierEntity::TYPE_TRAIN == $contract->getCarrierType()) {
                            if (isset($companyToPlantDebtTransaction)) {
                                // Компания должна заводу - минус компании
                                $this->financeManager->saveTransaction($companyToPlantDebtTransaction);
                            }
                            if (isset($companyToPlantCreditTransaction)) {
                                // Заводу должна компания - плюс заводу
                                $this->financeManager->saveTransaction($companyToPlantCreditTransaction);
                            }
                            // Завод должен перевозчику - минус заводу
                            $this->financeManager->saveTransaction($carrierTransaction);
                        } else {
                            // Компания должна перевозчику - минус компании
                            $carrierTransaction->setCompanyId($companyContractor->getContractorId());
                            $carrierTransaction->setComment(sprintf('Авто. Перевозка продукции по договору %s за авто %s', $contract->getContractNumber(), $object->getWagonNumber()));
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }
                    }
                    break;
                default:
                    throw new Exception\InvalidArgumentException('Invalid delivery condition type');
            }

            $warehouseTransaction = new WarehouseLogEntity();
            $warehouseTransaction->setContractorId($contract->getCustomerId());
            $warehouseTransaction->setWarehouseId($contract->getWarehouseId());
            $warehouseTransaction->setResourceId($contract->getProductId());
            $warehouseTransaction->setWagonId($object->getWagonId());
            $warehouseTransaction->setResourceWeight($object->getLoadingWeight());
            $warehouseTransaction->setComment(sprintf('Отгрузка продукции по договору %s в вагон %s',
                $contract->getContractNumber(), $object->getWagonNumber()));
            $warehouseTransaction->setCreated($object->getLoadingDate());

            $this->warehouseLogManager->output($warehouseTransaction);

            // Фиксирование транзакции добавления вагон(а/ов)
            $this->saleContractDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения ErrorException
            $this->saleContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        } catch (Exception\InvalidArgumentException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения InvalidArgumentException
            $this->saleContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }

    /**
     * @param $wagonId
     * @return Service\Result
     */
    public function deleteWagonById($wagonId) {
        try {
            // Удаление старых записей о задолженностях
            $this->financeManager->deleteTransactionByWagonId($wagonId, TransactionEntity::CONTRACT_SALE);
            // Удаление старых записей по складах
            $this->warehouseLogManager->deleteLogByWagonId($wagonId);
            $wagonId = intval($wagonId);
            $this->saleWagonDbRepository->deleteWagonById($wagonId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data has been deleted');
    }

    public function deleteWagonMultiple($ids) {
        $ids = explode(',', $ids);
        $this->saleWagonDbRepository->beginTransaction();
        try {
            foreach ($ids as $wagonId) {
                if ($wagonId = intval($wagonId)) {
                    // Удаление старых записей о задолженностях
                    $this->financeManager->deleteTransactionByWagonId($wagonId, TransactionEntity::CONTRACT_SALE);
                    // Удаление старых записей по складах
                    $this->warehouseLogManager->deleteLogByWagonId($wagonId);
                    $wagonId = intval($wagonId);
                    $this->saleWagonDbRepository->deleteWagonById($wagonId);
                }
            }
            $this->saleWagonDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            $this->saleWagonDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagons were successfully deleted');
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The wagons were not deleted');
    }


    /**
     * @param            $contractId
     * @param array|null $filterData
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getWagonsExportData($contractId, array $filterData = null) {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getSheet(0);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheetRowsCount = 0;

        $sheet->fromArray([
            'Номер вагона',
            'Вес загрузки',
            'Дата загрузки',
            'Стоимость',
        ], null, sprintf('A%s', ++$sheetRowsCount));

        $sheet->getStyle(sprintf('A%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle(sprintf('B%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle(sprintf('D%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


        $sheet->getStyle(sprintf('A%1$s:J%1$s', $sheetRowsCount))->getFont()->setBold(true);

        $wagons = $this->saleWagonDbRepository->fetchWagonsArray($contractId, $filterData);


        foreach ($wagons as $wagon) {
            $source = [
                $wagon['wagon_number'],
                $wagon['loading_weight'],
                \DateTime::createFromFormat('Y-m-d', $wagon['loading_date'])->format('d.m.Y'),
                $wagon['product_price'],
            ];

            $sheet->fromArray($source, null, sprintf('A%s', ++$sheetRowsCount));

            $sheet->getStyle(sprintf('A%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle(sprintf('B%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $sheet->getStyle(sprintf('D%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('D%s', $sheetRowsCount))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
        }

        $filename = realpath('./www') . '/export/results.xlsx';
        unlink($filename);
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($filename);
        return $filename;
    }

    /**
     * @param SaleContractEntity $contract
     * @param                    $data
     * @param                    $file
     * @return Service\Result
     * @throws \Contractor\Exception\ErrorException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function putWagonsImportData(SaleContractEntity $contract, $data, $file) {
        $this->saleContractDbRepository->beginTransaction();
        try {
            $reader = new XlsxReader();
            //$spreadsheet = $reader->load('./www/export/43453-sale-wagons-export.xlsx');
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        } catch (\InvalidArgumentException $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        }

        $errors = [];

        $rateObj = $this->rateManager->getRateById($data['rate_id']);

        if (($contract::CONDITIONS_TYPE_FCA != $contract->getConditions()) && !$rateObj) {
            $errors[] = 'Указано недопустимое значание тарифа';
        } else {

            $sheet = $spreadsheet->getActiveSheet();

            /** @var ClassMethods $hydrator */
            $hydrator = clone $this->saleWagonDbRepository->getHydrator();
            $hydrator->addStrategy('loading_date', new DateTimeFormatterStrategy('d.m.Y'));

            $rateValues = $rateObj ? $rateObj->getValues() : [];

            foreach ($sheet->getRowIterator() as $key => $row) {
                $errorsMessages = [];

                $rowIndex = $row->getRowIndex();
                if ($rowIndex < 2) continue;

                $wagonNumber = $sheet->getCell(sprintf('A%d', $rowIndex))->getValue();
                if (!trim($wagonNumber))
                    break;

                $wagon = new SaleWagonEntity();
                $wagon->setWagonNumber($wagonNumber);
                $wagon->setContractId($data['contract_id']);

                $wagon->setCarrierId(intval($data['carrier_id']));
                $wagon->setStatus($wagon::STATUS_LOADED);
                $wagon->setProductPrice($contract->getPrice(true) * $sheet->getCell(sprintf('B%d', $rowIndex))->getValue());

                $loadingWeight = $sheet->getCell(sprintf('B%d', $rowIndex))->getValue();

                $loadingDate = $sheet->getCell(sprintf('C%d', $rowIndex))->getValue();
                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $loadingDate)) {
                    $loadingDate = \DateTime::createFromFormat('d.m.Y', $loadingDate);
                } else {
                    $loadingDate = PhpSpreadsheetSharedDate::excelToDateTimeObject($loadingDate);
                }
                $loadingDate->setTime(23, 59, 59);


                $filteredIterator = new \CallbackFilterIterator(new \ArrayIterator($rateValues), function (RateValueEntity $rateValue) use ($loadingWeight) {
                    $between = explode('-', $rateValue->getWeight());
                    if (1 < count($between) && $loadingWeight < $between[1] && $loadingWeight > $between[0]) {
                        return true;
                    }
                    return false;
                });

                $filteredData = iterator_to_array($filteredIterator);

                if (!count($filteredData) && ($contract::CONDITIONS_TYPE_FCA != $contract->getConditions())) {
                    $errorsMessages[] = sprintf('Указан недопустимый вес в строке %d', $rowIndex);
                }

                if ($loadingDate < $contract->getCreated()) {
                    $errorsMessages[] = sprintf('Указана недопустимая дата загрузки в строке %d', $rowIndex);
                }

                if (count($errorsMessages)) {
                    $errors = array_merge($errors, $errorsMessages);
                    continue;
                }

                if (count($filteredData)) {
                    /** @var RateValueEntity $rateValue */
                    $rateValue = current($filteredData);
                    $wagon->setRateValueId($rateValue->getValueId());
                } else {
                    $wagon->setRateValueId(0);
                }


                $wagon->setRateId(intval($data['rate_id']));

                $wagon->setLoadingWeight($loadingWeight);

                $wagon->setLoadingDate($loadingDate);

                $this->saleWagonInputFilter->setData($hydrator->extract($wagon));
                $this->saleWagonInputFilter->remove('csrf');
                $this->saleWagonInputFilter->remove('rate_id');
                $this->saleWagonInputFilter->remove('carrier_id');
                $this->saleWagonInputFilter->remove('loading_date');

                if ($this->saleWagonInputFilter->isValid() && !count($errors)) {
                    $this->saveWagon($wagon);
                } else {
                    $errors[] = sprintf('Неизвестная ошибка в строке %d', $rowIndex);
                }
            }
        }

        if (0 < count($errors)) {
            $this->saleContractDbRepository->rollback();
            return new Service\Result('warning', 'Some wagon data was not imported.', $errors);
        }
        $this->saleContractDbRepository->commit();
        return new Service\Result('success', 'All wagon data was successfully imported.');


    }

}