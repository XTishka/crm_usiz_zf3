<?php

namespace Document\Service;

use Application\Service;
use Contractor\Entity\ContractorCompany;
use Contractor\Service\ContractorCompanyManager;
use Document\Domain\PurchaseContractEntity;
use Document\Domain\PurchaseWagonEntity;
use Document\Domain\TransactionEntity;
use Document\Exception;
use Document\InputFilter;
use Document\Service\Rate\AdapterFactory;
use Document\Service\Rate\MixedAdapter;
use Document\Service\Repository\PurchaseContractDb;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Service\WarehouseLogManager;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetSharedDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Transport\Domain\CarrierEntity;
use Transport\Domain\RateValueEntity;
use Transport\Service\CarrierManager;
use Transport\Service\RateManager;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;

class PurchaseWagonManager {

    /**
     * @var PurchaseContractDb
     */
    protected $purchaseContractDbRepository;

    /**
     * @var  Repository\PurchaseWagonDb
     */
    protected $purchaseWagonDbRepository;

    protected $purchaseWagonInputFilter;

    /**
     * @var FinanceManager
     */
    protected $financeManager;

    /**
     * @var RateManager
     */
    protected $rateManager;

    /**
     * @var CarrierManager
     */
    protected $carrierManager;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * @var ContractorCompanyManager
     */
    protected $contractorCompanyManager;

    /**
     * PurchaseWagonManager constructor.
     * @param PurchaseContractDb         $purchaseContractDbRepository
     * @param Repository\PurchaseWagonDb $purchaseWagonDbRepository
     * @param InputFilter\PurchaseWagon  $purchaseWagonInputFilter
     * @param FinanceManager             $financeManager
     * @param RateManager                $rateManager
     * @param CarrierManager             $carrierManager
     * @param WarehouseLogManager        $warehouseLogManager
     */
    public function __construct(PurchaseContractDb $purchaseContractDbRepository,
                                Repository\PurchaseWagonDb $purchaseWagonDbRepository,
                                InputFilter\PurchaseWagon $purchaseWagonInputFilter,
                                FinanceManager $financeManager,
                                RateManager $rateManager,
                                CarrierManager $carrierManager,
                                WarehouseLogManager $warehouseLogManager,
                                ContractorCompanyManager $contractorCompanyManager) {
        $this->purchaseContractDbRepository = $purchaseContractDbRepository;
        $this->purchaseWagonDbRepository = $purchaseWagonDbRepository;
        $this->purchaseWagonInputFilter = $purchaseWagonInputFilter;
        $this->financeManager = $financeManager;
        $this->rateManager = $rateManager;
        $this->carrierManager = $carrierManager;
        $this->warehouseLogManager = $warehouseLogManager;
        $this->contractorCompanyManager = $contractorCompanyManager;
    }

    public function getExpectedMaterialWeight($companyId, $date = null) {
        $companyId = intval($companyId);
        return $this->purchaseWagonDbRepository->fetchExpectedMaterialWeight($companyId, $date);
    }

    /**
     * @param $plantId
     * @param $companyId
     * @return array
     * @throws \Manufacturing\Exception\ErrorException
     */
    public function getTotalMaterialWeight($plantId, $companyId) {
        $companyId = intval($companyId);
        //$start = microtime(true);
        $warehouseMaterials = $this->warehouseLogManager->getMaterialBalances($plantId);
        //echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';
        $expectedMaterials = $this->purchaseWagonDbRepository->fetchExpectedMaterialWeight($companyId);

        $materialStack = [];
        foreach ($warehouseMaterials as $warehouseMaterial) {
            $hash = md5($warehouseMaterial['material_name']);
            if (!key_exists($hash, $materialStack)) {
                $materialStack[$hash] = [
                    'material_name' => $warehouseMaterial['material_name'],
                    'amount'        => $warehouseMaterial['price'],
                    'weight'        => $warehouseMaterial['weight'],
                ];
            } else {
                $materialStack[$hash]['amount'] += $warehouseMaterial['price'];
                $materialStack[$hash]['weight'] += $warehouseMaterial['weight'];
            }
        }

        foreach ($expectedMaterials as $expectedMaterial) {
            $hash = md5($expectedMaterial['material_name']);
            if (!key_exists($hash, $materialStack)) {
                $materialStack[$hash] = [
                    'material_name' => $expectedMaterial['material_name'],
                    'amount'        => $expectedMaterial['amount'],
                    'weight'        => $expectedMaterial['weight'],
                ];
            } else {
                $materialStack[$hash]['amount'] += $expectedMaterial['amount'];
                $materialStack[$hash]['weight'] += $expectedMaterial['weight'];
            }
        }

        return $materialStack;
    }

    public function getExpectedWagonsStatistic($companyId) {
        $companyId = intval($companyId);
        return $this->purchaseWagonDbRepository->fetchExpectedWagonsStatistic($companyId);
    }

    /**
     * @param null $contractId
     * @param null $filterData
     * @return \Zend\Paginator\Paginator
     */
    public function getWagonsPaginator($contractId = null, $filterData = null) {
        $paginator = $this->purchaseWagonDbRepository->fetchWagonsPaginator($contractId, $filterData);
        return $paginator;
    }

    /**
     * @param $rateId
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getWagonsByRateId($rateId) {
        return $this->purchaseWagonDbRepository->fetchWagonsByRateId($rateId);
    }

    /**
     * @param $wagonId
     * @return PurchaseWagonEntity
     */
    public function getWagonById($wagonId) {
        $wagonId = intval($wagonId);
        $purchaseWagon = $this->purchaseWagonDbRepository->fetchWagonById($wagonId);
        return $purchaseWagon;
    }

    public function unloadWagonMultiple($ids, PurchaseContractEntity $contract, $qDate = null) {
        $ids = explode(',', $ids);
        if (!$qDate) {
            $date = new \DateTime();
        } else {
            $date = \DateTime::createFromFormat('d.m.Y', $qDate);
        }

        $this->purchaseWagonDbRepository->beginTransaction();
        try {
            foreach ($ids as $id) {
                if ($id = intval($id)) {
                    $wagon = $this->getWagonById($id);

                    if (PurchaseWagonEntity::STATUS_UNLOADED === $wagon->getStatus())
                        continue;

                    $wagon->setStatus($wagon::STATUS_UNLOADED);
                    $wagon->setUnloadingDate($date);
                    $wagon->setUnloadingWeight($wagon->getLoadingWeight());

                    $this->purchaseWagonDbRepository->saveWagon($wagon);

                    $warehouseLog = new WarehouseLogEntity();
                    $warehouseLog->setContractorId($contract->getProviderId());
                    $warehouseLog->setCreated($wagon->getUnloadingDate());
                    $warehouseLog->setDirection($warehouseLog::DIRECTION_INPUT);
                    $warehouseLog->setResourceId($contract->getMaterialId());
                    $warehouseLog->setResourcePrice($wagon->getMaterialPrice() + $wagon->getDeliveryPrice());
                    $warehouseLog->setResourceWeight($wagon->getUnloadingWeight());
                    $warehouseLog->setWagonId($wagon->getWagonId());
                    $warehouseLog->setWarehouseId($contract->getWarehouseId());
                    $warehouseLog->setComment(sprintf('Разгрузка сырья по договору %s, вагон %s',
                        $contract->getContractNumber(), $wagon->getWagonNumber()));

                    $this->warehouseLogManager->input($warehouseLog);
                }
            }
            $this->purchaseWagonDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            $this->purchaseWagonDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagon data was not saved.');
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }

    /**
     * @param PurchaseWagonEntity $object
     * @return Service\Result
     */
    public function unloadWagon(PurchaseWagonEntity $object) {
        try {
            $object->setStatus($object::STATUS_UNLOADED);
            $this->purchaseWagonDbRepository->saveWagon($object);

            $contract = $this->purchaseContractDbRepository->fetchContractById($object->getContractId());

            $warehouseLog = new WarehouseLogEntity();
            $warehouseLog->setContractorId($contract->getProviderId());
            $warehouseLog->setCreated($object->getUnloadingDate());
            $warehouseLog->setDirection($warehouseLog::DIRECTION_INPUT);
            $warehouseLog->setResourceId($contract->getMaterialId());
            $warehouseLog->setResourcePrice($object->getMaterialPrice() + $object->getDeliveryPrice());
            $warehouseLog->setResourceWeight($object->getUnloadingWeight());
            $warehouseLog->setWagonId($object->getWagonId());
            $warehouseLog->setWarehouseId($contract->getWarehouseId());
            $warehouseLog->setComment(sprintf('Разгрузка сырья по договору %s, вагон %s',
                $contract->getContractNumber(), $object->getWagonNumber()));

            $this->warehouseLogManager->input($warehouseLog);

        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagon data was not saved.');
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }

    /**
     * @param PurchaseWagonEntity $object
     * @return Service\Result
     */
    public function loadWagon(PurchaseWagonEntity $object) {
        if (!count($wagons = $object->getWagons()))
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagon data was not saved.');
        try {
            // Начало транзакции загрузки вагона
            $this->purchaseContractDbRepository->beginTransaction();

            $contract = $this->purchaseContractDbRepository->fetchContractById($object->getContractId());

            // Рассчет стоимости доставки по умолчанию

            /** @var \ArrayObject $wagon */
            foreach ($wagons as $wagon) {

                /*
                echo '<pre>';
                echo "Цена с НДС: " . $contract->getPrice(true) . "\n\n";
                echo "Вес загрузки: " . $wagon->offsetGet('loading_weight') . "\n\n";
                */
                $materialPrice = bcmul($contract->getPrice(true), $wagon->offsetGet('loading_weight'), 5);

                // exit($materialPrice);

                // Формирование обьекта одиночного вагона
                $currentWagon = clone $object;
                $currentWagon->setWagons(null);
                $currentWagon->setStatus($currentWagon::STATUS_LOADED);
                $currentWagon->setWagonNumber($wagon->offsetGet('wagon_number'));
                $currentWagon->setMaterialPrice($materialPrice);
                $currentWagon->setTransportPrice($wagon->offsetGet('transport_price'));
                $currentWagon->setTransportComment($wagon->offsetGet('transport_comment'));
                $currentWagon->setLoadingWeight($wagon->offsetGet('loading_weight'));
                $currentWagon->setRateId($object->getRateId());
                $currentWagon->setRateValueId($wagon->offsetExists('rate_value_id') ? $wagon->offsetGet('rate_value_id') : 0);


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

                $currentWagon->setMaterialPrice($materialPrice);
                $currentWagon->setDeliveryPrice($debtToCarrier ?? 0);
                $currentWagon->setStatus($currentWagon::STATUS_LOADED);
                $currentWagon = $this->purchaseWagonDbRepository->saveWagon($currentWagon);

                /*
                //if ($currentWagon->getRateId()) {
                $transportRate = $this->rateManager->getRateById($currentWagon->getRateId());

                // Рассчет стоимости доставки по умолчанию
                $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRate->getPrice(), $currentWagon->getLoadingWeight());
                if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                    $rateAdapter->setMinWeight($minWeight);
                }
                $debtToCarrier = $rateAdapter->calculate($contract->getTax());
                */

                if (isset($transportRate)) {
                    // Формирование задолженности перед перевозчиком
                    $carrierTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CARRIER);
                    $carrierTransaction->setCompanyId($contract->getCompanyId());
                    $carrierTransaction->setContractorId($transportRate->getCarrierId() ?? 0);
                    $carrierTransaction->setContractType($carrierTransaction::CONTRACT_PURCHASE);
                    $carrierTransaction->setCredit($debtToCarrier);
                    $carrierTransaction->setWagonId($currentWagon->getWagonId());
                    $carrierTransaction->setComment(sprintf('Перевозка сырья по договору %s за вагон %s',
                        $contract->getContractNumber(), $currentWagon->getWagonNumber()));
                    //}
                }


                // Формирование задолженности перед поставщиком
                $providerTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_PROVIDER);
                $providerTransaction->setCompanyId($contract->getCompanyId());
                $providerTransaction->setContractorId($contract->getProviderId());
                $providerTransaction->setContractType(TransactionEntity::CONTRACT_PURCHASE);
                $providerTransaction->setCredit($materialPrice);
                $providerTransaction->setWagonId($currentWagon->getWagonId());
                $providerTransaction->setComment(sprintf('Поставка сырья по договору %s за вагон %s',
                    $contract->getContractNumber(), $currentWagon->getWagonNumber()));

                // Фиксирование задолженностей в зависимости от условий доставки
                switch ($contract->getConditions()) {
                    // Записываеться задолженность перед поставщиком за сырье и перед перевозчиком
                    case $contract::CONDITIONS_TYPE_FCA:
                        $this->financeManager->saveTransaction($providerTransaction);
                        if (isset($carrierTransaction)) {
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }
                        break;
                    // Записываеться задолженность только перед поставщиком за сырье
                    case $contract::CONDITIONS_TYPE_CPT:
                        $this->financeManager->saveTransaction($providerTransaction);
                        break;
                    // Записываеться задолженность перед поставщиком за сырье и за перевозку
                    case $contract::CONDITIONS_TYPE_CPT_RETURN:
                        $this->financeManager->saveTransaction($providerTransaction);
                        if (isset($carrierTransaction)) {
                            $carrierTransaction->setContractorType($carrierTransaction::CONTRACTOR_PROVIDER);
                            $carrierTransaction->setContractorId($contract->getProviderId());
                            $this->financeManager->saveTransaction($carrierTransaction);
                        }
                        break;
                    default:
                        throw new Exception\InvalidArgumentException('Invalid delivery condition type');
                }

                // Если указаны дополнительные траты по вагону устанавливает задолженость перед указанным контрагентом
                if (0 < $transportPrice = $wagon->offsetGet('transport_price')) {
                    $extraTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_ADDITIONAL);
                    $extraTransaction->setCompanyId($contract->getCompanyId());
                    $extraTransaction->setContractorId($wagon->offsetGet('contractor_id'));
                    $extraTransaction->setContractType(TransactionEntity::CONTRACT_PURCHASE);
                    $extraTransaction->setCredit(Service\TaxManager::calculate($currentWagon->getTransportPrice(), $contract->getTax()));
                    $extraTransaction->setWagonId($currentWagon->getWagonId());
                    $extraTransaction->setComment($currentWagon->getTransportComment());
                    // Сохранение задолженности в базу данных
                    $this->financeManager->saveTransaction($extraTransaction);
                }

            }
            // Фиксирование транзакции добавления вагон(а/ов)
            $this->purchaseContractDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения ErrorException
            $this->purchaseContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        } catch (Exception\InvalidArgumentException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения InvalidArgumentException
            $this->purchaseContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }

    public function saveWagon(PurchaseWagonEntity $object) {
        try {
            // Начало транзакции загрузки вагона
            $this->purchaseContractDbRepository->beginTransaction();

            $contract = $this->purchaseContractDbRepository->fetchContractById($object->getContractId());

            $materialPrice = bcmul($contract->getPrice(true), $object->getLoadingWeight(), 2);

            // Рассчет стоимости доставки по умолчанию
            if (($rateId = $object->getRateId()) && ($rateValueId = $object->getRateValueId())) {

                $transportRate = $this->rateManager->getRateById($rateId);
                $transportRateValue = $this->rateManager->getRateValueById($rateValueId);

                //echo '<pre>';print_r($transportRate);exit;

                // Рассчет стоимости доставки по умолчанию
                $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRateValue->getPrice(), $object->getLoadingWeight());
                if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                    //exit($minWeight);
                    $rateAdapter->setMinWeight($minWeight);
                }
                $debtToCarrier = $contract::CONDITIONS_TYPE_CPT != $contract->getConditions() ? $rateAdapter->calculate($contract->getTax()) : 0;
                //file_put_contents('debt.txt', $debtToCarrier . "'n'");
            }

            $object->setMaterialPrice($materialPrice);
            $object->setDeliveryPrice($debtToCarrier ?? 0);
            if (!$object->getStatus())
                $object->setStatus($object::STATUS_LOADED);

            $object = $this->purchaseWagonDbRepository->saveWagon($object);

            if (isset($debtToCarrier) && isset($transportRate)) {

                //if (CarrierEntity::TYPE_TRAIN == $contract->getCarrierType()) {
                    /** @var ContractorCompany $company */
                    /*
                    $company = $this->contractorCompanyManager->getContractorById($contract->getCompanyId());

                    // Формирование задолженности перед ЖД перевозчиком
                    $carrierTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CARRIER);
                    $carrierTransaction->setCompanyId($company->getPlantId());
                    $carrierTransaction->setContractorId($transportRate->getCarrierId() ?? 0);
                    $carrierTransaction->setContractType($carrierTransaction::CONTRACT_PURCHASE);
                    $carrierTransaction->setCredit($debtToCarrier);
                    $carrierTransaction->setWagonId($object->getWagonId());
                    $carrierTransaction->setComment(sprintf('Перевозка сырья по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                    $carrierTransaction->setCreated(clone ($object->getLoadingDate())->setTime(8, 0, 0));

                    $companyToPlantTransportTransaction = clone $carrierTransaction;
                    $companyToPlantTransportTransaction->setCompanyId($company->getContractorId());
                    $companyToPlantTransportTransaction->setContractorType($company::TYPE_PLANT);
                    $companyToPlantTransportTransaction->setContractorId($company->getPlantId());

                    $plantFromCompanyTransaction = clone $carrierTransaction;
                    $plantFromCompanyTransaction->setCompanyId($company->getPlantId());
                    $plantFromCompanyTransaction->setContractorType($company::TYPE_COMPANY);
                    $plantFromCompanyTransaction->setContractorId($company->getContractorId());
                    if ($plantFromCompanyTransaction->getCredit()) {
                        $plantFromCompanyTransaction->setDebit($plantFromCompanyTransaction->getCredit());
                        $plantFromCompanyTransaction->setCredit(0);
                    } else {
                        $plantFromCompanyTransaction->setCredit($plantFromCompanyTransaction->getDebit());
                        $plantFromCompanyTransaction->setDebit(0);
                    }

                */
                //} else {
                    // Формирование задолженности перед автотранспортным перевозчиком
                    $carrierTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_CARRIER);
                    $carrierTransaction->setCompanyId($contract->getCompanyId());
                    $carrierTransaction->setContractorId($transportRate->getCarrierId() ?? 0);
                    $carrierTransaction->setContractType($carrierTransaction::CONTRACT_PURCHASE);
                    $carrierTransaction->setCredit($debtToCarrier);
                    $carrierTransaction->setWagonId($object->getWagonId());
                    $carrierTransaction->setComment(sprintf('Перевозка сырья по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                    $carrierTransaction->setCreated(clone ($object->getLoadingDate())->setTime(8, 0, 0));
                //}
            }

            // Удаление старых записей о задолженностях
            $this->financeManager->deleteTransactionByWagonId($object->getWagonId(), TransactionEntity::CONTRACT_PURCHASE);
            // Удаление старых записей по складах
            $this->warehouseLogManager->deleteLogByWagonId($object->getWagonId());

            // Формирование задолженности перед поставщиком
            $providerTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_PROVIDER);
            $providerTransaction->setCompanyId($contract->getCompanyId());
            $providerTransaction->setContractorId($contract->getProviderId());
            $providerTransaction->setContractType(TransactionEntity::CONTRACT_PURCHASE);
            $providerTransaction->setCredit($materialPrice);
            $providerTransaction->setWagonId($object->getWagonId());
            $providerTransaction->setComment(sprintf('Поставка сырья по договору %s за вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
            $providerTransaction->setCreated(clone ($object->getLoadingDate())->setTime(8, 0, 0));

            // Фиксирование задолженностей в зависимости от условий доставки
            switch ($contract->getConditions()) {
                // Записываеться задолженность перед поставщиком за сырье и перед перевозчиком
                case $contract::CONDITIONS_TYPE_FCA:

                    $this->financeManager->saveTransaction($providerTransaction);
                    if (isset($carrierTransaction)) {
                        $this->financeManager->saveTransaction($carrierTransaction);
                    }
                    if (isset($plantTransportTransaction)) {
                        $this->financeManager->saveTransaction($plantTransportTransaction);
                    }
                    if (isset($companyToPlantTransportTransaction)) {
                        $this->financeManager->saveTransaction($companyToPlantTransportTransaction);
                    }
                    if (isset($plantFromCompanyTransaction)) {
                        $this->financeManager->saveTransaction($plantFromCompanyTransaction);
                    }
                    break;
                // Записываеться задолженность только перед поставщиком за сырье
                case $contract::CONDITIONS_TYPE_CPT:
                    $this->financeManager->saveTransaction($providerTransaction);
                    break;
                // Записываеться задолженность перед поставщиком за сырье и за перевозку
                case $contract::CONDITIONS_TYPE_CPT_RETURN:
                    $this->financeManager->saveTransaction($providerTransaction);
                    if (isset($carrierTransaction)) {
                        $carrierTransaction->setContractorType($carrierTransaction::CONTRACTOR_PROVIDER);
                        $carrierTransaction->setContractorId($contract->getProviderId());
                        $this->financeManager->saveTransaction($carrierTransaction);
                    }
                    if (isset($plantTransportTransaction)) {
                        $this->financeManager->saveTransaction($plantTransportTransaction);
                    }
                    if (isset($companyToPlantTransportTransaction)) {
                        $this->financeManager->saveTransaction($companyToPlantTransportTransaction);
                    }
                    if (isset($plantFromCompanyTransaction)) {
                        $this->financeManager->saveTransaction($plantFromCompanyTransaction);
                    }
                    break;
                default:
                    throw new Exception\InvalidArgumentException('Invalid delivery condition type');
            }

            // Если указаны дополнительные траты по вагону устанавливает задолженость перед указанным контрагентом
            if (0 < $transportPrice = $object->getTransportPrice()) {
                $extraTransaction = new TransactionEntity(TransactionEntity::TRANSACTION_DEBT, TransactionEntity::CONTRACTOR_ADDITIONAL);
                $extraTransaction->setCompanyId($contract->getCompanyId());
                $extraTransaction->setContractorId($object->getTransportContractorId());
                $extraTransaction->setContractType(TransactionEntity::CONTRACT_PURCHASE);
                $extraTransaction->setCredit(Service\TaxManager::calculate($object->getTransportPrice(), $contract->getTax()));
                $extraTransaction->setWagonId($object->getWagonId());
                $extraTransaction->setComment($object->getTransportComment());
                $extraTransaction->setCreated(clone ($object->getLoadingDate())->setTime(8, 0, 0));
                // Сохранение задолженности в базу данных
                $this->financeManager->saveTransaction($extraTransaction);
            }

            // Фиксирование транзакции добавления вагон(а/ов)
            $this->purchaseContractDbRepository->commit();

            if ($object->getStatus() == $object::STATUS_UNLOADED) {

                $warehouseLog = new WarehouseLogEntity();
                $warehouseLog->setContractorId($contract->getProviderId());
                $warehouseLog->setCreated($object->getUnloadingDate());
                $warehouseLog->setDirection($warehouseLog::DIRECTION_INPUT);
                $warehouseLog->setResourceId($contract->getMaterialId());
                $warehouseLog->setResourcePrice($object->getMaterialPrice() + $object->getDeliveryPrice());
                $warehouseLog->setResourceWeight($object->getUnloadingWeight());
                $warehouseLog->setWagonId($object->getWagonId());
                $warehouseLog->setWarehouseId($contract->getWarehouseId());
                $warehouseLog->setComment(sprintf('Разгрузка сырья по договору %s, вагон %s', $contract->getContractNumber(), $object->getWagonNumber()));
                $warehouseLog->setCreated(clone ($object->getLoadingDate())->setTime(8, 0, 0));

                $this->warehouseLogManager->input($warehouseLog);

            }

        } catch (Exception\ErrorException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения ErrorException
            $this->purchaseContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        } catch (Exception\InvalidArgumentException $exception) {
            // Откат транзакции добавления вагон(а/ов) при возникновении исключения InvalidArgumentException
            $this->purchaseContractDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data was successfully saved');
    }

    /**
     * @param $wagonId
     * @return Service\Result
     */
    public function deleteWagonById($wagonId) {
        $this->purchaseWagonDbRepository->beginTransaction();
        try {
            $wagonId = intval($wagonId);
            $this->purchaseWagonDbRepository->deleteWagonById($wagonId);
            // Удаление старых записей о задолженностях
            $this->financeManager->deleteTransactionByWagonId($wagonId);
            // Удаление старых записей по складах
            $this->warehouseLogManager->deleteLogByWagonId($wagonId);
            $this->purchaseWagonDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            $this->purchaseWagonDbRepository->rollback();
            throw $exception;
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Wagon data has been deleted');
    }

    public function deleteWagonMultiple($ids) {
        $ids = explode(',', $ids);
        $this->purchaseWagonDbRepository->beginTransaction();
        try {
            foreach ($ids as $wagonId) {
                if ($wagonId = intval($wagonId)) {
                    $this->purchaseWagonDbRepository->deleteWagonById($wagonId);
                    // Удаление старых записей о задолженностях
                    $this->financeManager->deleteTransactionByWagonId($wagonId);
                    // Удаление старых записей по складах
                    $this->warehouseLogManager->deleteLogByWagonId($wagonId);
                }
            }
            $this->purchaseWagonDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            $this->purchaseWagonDbRepository->rollback();
            return new Service\Result(Service\Result::STATUS_ERROR, 'The wagons were successfully deleted');
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The wagons were not deleted');
    }

    /**
     * @param            $contractId
     * @param array|null $filterData
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getWagonsExportData($contractId, array $filterData = null) {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getSheet(0);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $sheetRowsCount = 0;

        $sheet->fromArray([
            'Номер вагона',
            'Вес загрузки',
            'Дата загрузки',
            'Вес разгрузки',
            'Дата разгрузки',
        ], null, sprintf('A%s', ++$sheetRowsCount));

        $sheet->getStyle(sprintf('A%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle(sprintf('B%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle(sprintf('D%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle(sprintf('E%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


        $sheet->getStyle(sprintf('A%1$s:J%1$s', $sheetRowsCount))->getFont()->setBold(true);

        $wagons = $this->purchaseWagonDbRepository->fetchWagonsArray($contractId, $filterData);


        foreach ($wagons as $wagon) {
            $source = [
                $wagon['wagon_number'],
                $wagon['loading_weight'],
                \DateTime::createFromFormat('Y-m-d', $wagon['loading_date'])->format('d.m.Y'),
                $wagon['unloading_weight'] ? floatval($wagon['unloading_weight']) : null,
                trim($wagon['unloading_date']) ? \DateTime::createFromFormat('Y-m-d', $wagon['unloading_date'])->format('d.m.Y') : null,
            ];

            $sheet->fromArray($source, null, sprintf('A%s', ++$sheetRowsCount));

            $sheet->getStyle(sprintf('A%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle(sprintf('B%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('C%s', $sheetRowsCount))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $sheet->getStyle(sprintf('D%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('E%s', $sheetRowsCount))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle(sprintf('E%s', $sheetRowsCount))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
        }

        $filename = realpath('./www') . '/export/results.xlsx';
        unlink($filename);
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        return $filename;
    }

    /**
     * @param PurchaseContractEntity $contract
     * @param                        $data
     * @param                        $file
     * @return Service\Result
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function putWagonsImportData(PurchaseContractEntity $contract, $data, $file) {
        $this->purchaseWagonDbRepository->beginTransaction();
        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            //$spreadsheet = $reader->load('./www/export/43453-purchase-wagons-export.xlsx');
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        } catch (\InvalidArgumentException $exception) {
            return new Service\Result('error', sprintf('The wagon data was not imported. (%s)', $exception->getMessage()));
        }

        $errors = [];

        $rateObj = $this->rateManager->getRateById($data['rate_id']);

        if (($contract::CONDITIONS_TYPE_CPT != $contract->getConditions()) && !$rateObj) {
            $errors[] = 'Указано недопустимое значание тарифа';
        } else {

            ///echo '<pre>'; print_r($valuesIterator); exit;

            $sheet = $spreadsheet->getActiveSheet();

            $hydrator = clone $this->purchaseWagonDbRepository->getHydrator();
            $hydrator->addStrategy('loading_date', new DateTimeFormatterStrategy('d.m.Y'));
            $hydrator->addStrategy('unloading_date', new DateTimeFormatterStrategy('d.m.Y'));

            $rateValues = $rateObj ? $rateObj->getValues() : [];

            foreach ($sheet->getRowIterator() as $key => $row) {
                $errorsMessages = [];

                $rowIndex = $row->getRowIndex();
                if ($rowIndex < 2) continue;

                $wagonNumber = $sheet->getCell(sprintf('A%d', $rowIndex))->getValue();
                if (!trim($wagonNumber))
                    break;

                $wagon = new PurchaseWagonEntity();
                $wagon->setWagonNumber($wagonNumber);
                $wagon->setContractId($data['contract_id']);

                $wagon->setCarrierId(intval($data['carrier_id']));
                $wagon->setStatus($wagon::STATUS_LOADED);
                $wagon->setMaterialPrice($contract->getPrice(true) * $sheet->getCell(sprintf('B%d', $rowIndex))->getValue());
                //$wagon->setDeliveryPrice()

                $loadingWeight = $sheet->getCell(sprintf('B%d', $rowIndex))->getValue();
                $unloadingWeight = $sheet->getCell(sprintf('D%d', $rowIndex))->getValue();

                $filteredIterator = new \CallbackFilterIterator(new \ArrayIterator($rateValues), function (RateValueEntity $rateValue) use ($loadingWeight, $rateObj) {
                    $between = explode('-', $rateValue->getWeight());

                    if ($loadingWeight < $between[0] && 'mixed' == $rateObj->getRateType()) {
                        return true;
                    }

                    if ((1 < count($between) && $loadingWeight <= $between[1] && $loadingWeight >= $between[0])) {
                        return true;
                    }
                    return false;
                });

                $filteredData = iterator_to_array($filteredIterator);
                $loadingDate = $sheet->getCell(sprintf('C%d', $rowIndex))->getValue();
                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $loadingDate)) {
                    $loadingDate = \DateTime::createFromFormat('d.m.Y', $loadingDate);
                } else {
                    $loadingDate = PhpSpreadsheetSharedDate::excelToDateTimeObject($loadingDate);
                }
                $loadingDate->setTime(23, 59, 59);

                $unloadingDate = $sheet->getCell(sprintf('E%d', $rowIndex))->getValue();
                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $unloadingDate)) {
                    $unloadingDate = \DateTime::createFromFormat('d.m.Y', $unloadingDate);
                    $unloadingDate->setTime(23, 59, 59);
                } elseif (preg_match('/^\d+$/', $unloadingDate)) {
                    $unloadingDate = PhpSpreadsheetSharedDate::excelToDateTimeObject($unloadingDate);
                    $unloadingDate->setTime(23, 59, 59);
                } else {
                    $unloadingDate = null;
                }


                //if (($contract::CONDITIONS_TYPE_CPT != $contract->getConditions()) || !count($filteredData)) {
                if (($contract::CONDITIONS_TYPE_CPT != $contract->getConditions()) && !count($filteredData)) {
                    $errorsMessages[] = sprintf('Указан недопустимый вес в строке %d', $rowIndex);
                }

                if (($loadingDate < $contract->getCreated()) || ((new \DateTime())->setTime(23, 59, 59) < $loadingDate)) {
                    $errorsMessages[] = sprintf('Указана недопустимая дата загрузки в строке %d', $rowIndex);
                }

                if ($unloadingDate && $unloadingDate < $wagon->getLoadingDate()) {
                    $errorsMessages[] = sprintf('Указана недопустимая дата разгрузки в строке %d', $rowIndex);
                }

                if ($unloadingWeight && ($loadingWeight < $unloadingWeight)) {
                    $errorsMessages[] = sprintf('Вес разгрузки больше веса загрузки в строке %d', $rowIndex);
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

                if ($unloadingWeight && $unloadingDate) {
                    $wagon->setStatus($wagon::STATUS_UNLOADED);
                    $wagon->setUnloadingWeight($unloadingWeight);
                    $wagon->setUnloadingDate($unloadingDate);
                }


                $this->purchaseWagonInputFilter->setData($hydrator->extract($wagon));
                $this->purchaseWagonInputFilter->remove('csrf');
                $this->purchaseWagonInputFilter->remove('rate_id');
                $this->purchaseWagonInputFilter->remove('carrier_id');
                $this->purchaseWagonInputFilter->remove('loading_date');
                $this->purchaseWagonInputFilter->remove('unloading_date');

                if ($this->purchaseWagonInputFilter->isValid() && !count($errors)) {
                    $this->saveWagon($wagon);
                } else {
                    $errors[] = sprintf('Неизвестная ошибка в строке %d', $rowIndex);
                }


            }

        }


        if (0 < count($errors)) {
            $this->purchaseWagonDbRepository->rollback();
            return new Service\Result('warning', 'Some wagon data was not imported.', $errors);
        }
        $this->purchaseWagonDbRepository->commit();
        return new Service\Result('success', 'All wagon data was successfully imported.');


    }

}