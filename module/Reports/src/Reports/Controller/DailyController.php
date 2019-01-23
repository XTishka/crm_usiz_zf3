<?php

namespace Reports\Controller;

use Application\Model\CheckingAccountService;
use Application\Model\Finance\AccountPayableService;
use Application\Model\Finance\PrepayFromCustomerService;
use Application\Model\Finance\DebtToProviderService;
use Application\Model\Finance\PrepayToProviderService;
use Application\Model\Finance\TotalReceivableService;
use Application\Service\Repository\ApiDb;
use Bank\Service\RecordManager;
use Contractor\Entity\ContractorAbstract;
use Contractor\Entity\ContractorCompany;
use Contractor\Service\ContractorCompanyManager;
//use Document\Domain\PurchaseWagonEntity;
use Manufacturing\Domain\WarehouseLogEntity;
use Reports\Form\DailyFilterForm;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DailyController extends AbstractActionController {

    /** @var Request */
    protected $request;

    /** @var AccountPayableService */
    protected $accountPayableService;

    /** @var TotalReceivableService */
    protected $totalReceivableService;

    /** @var CheckingAccountService */
    //protected $checkingAccountService;

    /**
     * @var PrepayFromCustomerService
     */
    protected $prepayFromCustomerService;

    /** @var Adapter */
    protected $db;

    /** @var ApiDb */
    protected $apiDbRepository;

    /** @var DailyFilterForm */
    protected $dailyFilterForm;

    /** @var ContractorCompanyManager */
    protected $contractorCompanyManager;

    /** @var RecordManager */
    protected $recordManager;

    /** @var DebtToProviderService */
    protected $prepayToProviderService;

    /**
     * DailyController constructor.
     * @param Adapter $db
     * @param AccountPayableService $accountPayableService
     * @param TotalReceivableService $totalReceivableService
     * @param PrepayFromCustomerService $prepayFromCustomerService
     * @param DailyFilterForm $dailyFilterForm
     * @param ContractorCompanyManager $contractorCompanyManager
     * @param ApiDb $apiDbRepository
     * @param RecordManager $recordManager
     * @param PrepayToProviderService $prepayToProviderService
     */
    public function __construct(
        Adapter $db,
        AccountPayableService $accountPayableService,
        TotalReceivableService $totalReceivableService,
        //CheckingAccountService $checkingAccountService,
        PrepayFromCustomerService $prepayFromCustomerService,
        DailyFilterForm $dailyFilterForm,
        ContractorCompanyManager $contractorCompanyManager,
        ApiDb $apiDbRepository,
        RecordManager $recordManager,
        PrepayToProviderService $prepayToProviderService) {
        $this->db = $db;

        $this->accountPayableService = $accountPayableService;
        $this->totalReceivableService = $totalReceivableService;
        //$this->checkingAccountService = $checkingAccountService;
        $this->prepayFromCustomerService = $prepayFromCustomerService;

        $this->dailyFilterForm = $dailyFilterForm;
        $this->contractorCompanyManager = $contractorCompanyManager;
        $this->apiDbRepository = $apiDbRepository;
        $this->recordManager = $recordManager;
        $this->prepayToProviderService = $prepayToProviderService;
    }


    /**
     * @return ViewModel
     * @throws \Bank\Exception\NotFoundException
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $form = $this->dailyFilterForm;
        $viewModel = new ViewModel();

        if ($this->request->isPost()) {
            $rawData = $this->params()->fromPost();
            $form->setData($rawData);
            if ($form->isValid()) {
                $filteredData = $form->getData();

                $date = $filteredData['date'];
                $date = \DateTime::createFromFormat('d.m.Y', $date);


                /** @var ContractorCompany $company */
                $company = $this->contractorCompanyManager->getContractorById($filteredData['company_id']);
                $plantId = $company->getPlantId();
                $warehouseMaterials = $this->getWarehouseMaterials($plantId, $date);
                $warehouseBeginMonth = $this->getBeginMonthWarehouseMaterials($plantId, $date);
                $purchaseMaterials = $this->getPurchaseMaterials($company->getContractorId(), $date);


                //$dataA = array_merge_recursive($warehouseMaterials, $warehouseBeginMonth, $purchaseMaterials);
                $dataA = [];

                foreach ($warehouseBeginMonth as $key => $item) {
                    if (key_exists($key, $warehouseMaterials)) {
                        $warehouseMaterials[$key]->contractorName = $item->contractorName;
                        $warehouseMaterials[$key]->materialType = $item->materialType;
                        $warehouseMaterials[$key]->purchasePrice = bcadd($warehouseMaterials[$key]->purchasePrice, $item->purchasePrice, 4);
                        $warehouseMaterials[$key]->purchaseWeight = bcadd($warehouseMaterials[$key]->purchaseWeight, $item->purchaseWeight, 4);
                        $warehouseMaterials[$key]->warehousePrice = bcadd($warehouseMaterials[$key]->warehousePrice, $item->warehousePrice, 4);
                        $warehouseMaterials[$key]->warehouseWeight = bcadd($warehouseMaterials[$key]->warehouseWeight, $item->warehouseWeight, 4);
                        $warehouseMaterials[$key]->monthPrice = bcadd($warehouseMaterials[$key]->monthPrice, $item->monthPrice, 4);
                        $warehouseMaterials[$key]->monthWeight = bcadd($warehouseMaterials[$key]->monthWeight, $item->monthWeight, 4);
                    } else {
                        $warehouseMaterials[$key] = new \stdClass();
                        $warehouseMaterials[$key]->contractorName = $item->contractorName;
                        $warehouseMaterials[$key]->materialType = $item->materialType;
                        $warehouseMaterials[$key]->purchasePrice = $item->purchasePrice;
                        $warehouseMaterials[$key]->purchaseWeight = $item->purchaseWeight;
                        $warehouseMaterials[$key]->warehousePrice = $item->warehousePrice;
                        $warehouseMaterials[$key]->warehouseWeight = $item->warehouseWeight;
                        $warehouseMaterials[$key]->monthPrice = $item->monthPrice;
                        $warehouseMaterials[$key]->monthWeight = $item->monthWeight;
                    }
                }

                foreach ($purchaseMaterials as $key => $item) {

                    if (key_exists($key, $warehouseMaterials)) {
                        $warehouseMaterials[$key]->contractorName = $item->contractorName;
                        $warehouseMaterials[$key]->materialType = $item->materialType;
                        $warehouseMaterials[$key]->purchasePrice = bcadd($warehouseMaterials[$key]->purchasePrice, $item->purchasePrice, 4);
                        $warehouseMaterials[$key]->purchaseWeight = bcadd($warehouseMaterials[$key]->purchaseWeight, $item->purchaseWeight, 4);
                        $warehouseMaterials[$key]->warehousePrice = bcadd($warehouseMaterials[$key]->warehousePrice, $item->warehousePrice, 4);
                        $warehouseMaterials[$key]->warehouseWeight = bcadd($warehouseMaterials[$key]->warehouseWeight, $item->warehouseWeight, 4);
                        $warehouseMaterials[$key]->monthPrice = bcadd($warehouseMaterials[$key]->monthPrice, $item->monthPrice, 4);
                        $warehouseMaterials[$key]->monthWeight = bcadd($warehouseMaterials[$key]->monthWeight, $item->monthWeight, 4);
                    } else {
                        $warehouseMaterials[$key] = new \stdClass();
                        $warehouseMaterials[$key]->contractorName = $item->contractorName;
                        $warehouseMaterials[$key]->materialType = $item->materialType;
                        $warehouseMaterials[$key]->purchasePrice = $item->purchasePrice;
                        $warehouseMaterials[$key]->purchaseWeight = $item->purchaseWeight;
                        $warehouseMaterials[$key]->warehousePrice = $item->warehousePrice;
                        $warehouseMaterials[$key]->warehouseWeight = $item->warehouseWeight;
                        $warehouseMaterials[$key]->monthPrice = $item->monthPrice;
                        $warehouseMaterials[$key]->monthWeight = $item->monthWeight;
                    }
                }

                $dataA = $warehouseMaterials;

                $data = array_map(function ($current) {
                    $current = (array)$current;
                    $totalPrice = 0;
                    $totalWeight = 0;
                    foreach ($current as $key => &$item) {
                        if ($key == 'purchasePrice' || $key == 'warehousePrice')
                            $totalPrice = bcadd($totalPrice, $item, 4);
                        if ($key == 'purchaseWeight' || $key == 'warehouseWeight')
                            $totalWeight = bcadd($totalWeight, $item, 4);
                    }
                    $current['totalPrice'] = $totalPrice;
                    $current['totalWeight'] = $totalWeight;
                    return $current;
                }, $dataA);

                $viewModel->setVariable('materials', $data);
                $viewModel->setVariable('customerDebts', $this->apiDbRepository->getCustomerDebtsPaginator(
                    $company->getContractorId(), $date->format('d.m.Y')));

                $viewModel->setVariable('bankRecords', $this->recordManager->getBankAmountRowset(
                    $company->getContractorId(), $date));

                /* ------------------------- */
                $this->prepayToProviderService->setDate($date);
                $this->accountPayableService->setDate($date);
                $this->prepayFromCustomerService->setDate($date);
                $this->totalReceivableService->setDate($date);

                $viewModel->setVariable('prepayFromCustomer', $this->prepayFromCustomerService->getRecords($company->getContractorId()));
                $viewModel->setVariable('prepayToProvider', $this->prepayToProviderService->getRecords($company->getContractorId()));
                $viewModel->setVariable('accountsPayable', $this->accountPayableService->getRecords($company->getContractorId(), true));
                $viewModel->setVariable('totalReceivable', $this->totalReceivableService->getRecords($company->getContractorId(), true));
                /* ------------------------- */
            }

        }

        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $this->plugin('flashMessenger'));
        return $viewModel;
    }

    /**
     * @param int $companyId
     * @param \DateTime|null $date
     * @throws \Exception
     */
    public function getFinanceTendency(int $companyId, \DateTime $date = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => 'finance_transactions']);
        $select->columns(['credit', 'debit', 'transaction_type']);
        $select->where->equalTo('company_id', $companyId);
        if ($date instanceof \DateTime) {
            $select->where->lessThanOrEqualTo('b.created', $date->format('Y-m-d'));
        }

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new \Exception('Could not retreive finance tendency');

        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);


    }


    /**
     * @param $plantId
     * @return array
     * @throws \Exception
     */
    function getWarehouseMaterials($plantId, $date = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => 'warehouses']);
        $select->columns([]);
        $select->join(['b' => 'warehouses_logs'], 'a.warehouse_id = b.warehouse_id', ['contractor_id', 'resource_price', 'resource_weight', 'direction']);
        $select->join(['c' => 'materials'], 'b.resource_id = c.material_id', ['material_name']);
        $select->join(['d' => 'material_types'], 'c.type_id = d.type_id', ['material_type' => 'name'], Join::JOIN_LEFT);
        $select->join(['e' => 'contractors'], 'b.contractor_id = e.contractor_id', ['contractor_name']);
        $select->where->equalTo('a.plant_id', $plantId);
        $select->where->equalTo('e.contractor_type', ContractorAbstract::TYPE_PROVIDER);
        $select->group('b.log_id');
        if ($date instanceof \DateTime) {
            $select->where->lessThanOrEqualTo('b.created', $date->format('Y-m-d'));
        }

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new \Exception('Could not retrieve leftovers for the warehouse');

        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

        $data = [];
        /** @var \ArrayObject $item */
        foreach ($resultSet as $item) {
            if (!key_exists(($index = 'i' . $item->offsetGet('contractor_id')), $data)) {
                $data[$index] = new \stdClass();
                $data[$index]->contractorName = $item->offsetGet('contractor_name');
                $data[$index]->materialType = $item->offsetGet('material_type');
                $data[$index]->purchasePrice = 0;
                $data[$index]->purchaseWeight = 0;
                $data[$index]->warehousePrice = 0;
                $data[$index]->warehouseWeight = 0;
                $data[$index]->monthPrice = 0;
                $data[$index]->monthWeight = 0;
            }
            if (WarehouseLogEntity::DIRECTION_INPUT == $item->offsetGet('direction')) {
                $data[$index]->warehousePrice = bcadd($data[$index]->warehousePrice, $item->offsetGet('resource_price'), 4);
                $data[$index]->warehouseWeight = bcadd($data[$index]->warehouseWeight, $item->offsetGet('resource_weight'), 4);
            } elseif (WarehouseLogEntity::DIRECTION_OUTPUT == $item->offsetGet('direction')) {
                $data[$index]->warehousePrice = bcsub($data[$index]->warehousePrice, $item->offsetGet('resource_price'), 4);
                $data[$index]->warehouseWeight = bcsub($data[$index]->warehouseWeight, $item->offsetGet('resource_weight'), 4);
            }
        }

        return $data;
    }

    /**
     * @param $plantId
     * @param null $date
     * @return array
     * @throws \Exception
     */
    function getBeginMonthWarehouseMaterials($plantId, $date = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => 'warehouses']);
        $select->columns([]);
        $select->join(['b' => 'warehouses_logs'], 'a.warehouse_id = b.warehouse_id', ['contractor_id', 'resource_price', 'resource_weight', 'direction', 'created']);
        $select->join(['c' => 'materials'], 'b.resource_id = c.material_id', ['material_name']);
        $select->join(['d' => 'material_types'], 'c.type_id = d.type_id', ['material_type' => 'name']);
        $select->join(['e' => 'contractors'], 'b.contractor_id = e.contractor_id', ['contractor_name']);

        $select->where->equalTo('a.plant_id', $plantId);
        $select->where->equalTo('e.contractor_type', ContractorAbstract::TYPE_PROVIDER);
        if ($date instanceof \DateTime) {
            $beginMonth = clone $date;
            $beginMonth->modify('first day of this month');
            $select->where->lessThan('b.created', $beginMonth->format('Y-m-d'));
        }

        //echo $select->getSqlString($this->db->platform);exit;

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new \Exception('Could not retrieve leftovers for the warehouse');

        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

        $data = [];
        /** @var \ArrayObject $item */
        foreach ($resultSet as $item) {
            if (!key_exists(($index = 'i' . $item->offsetGet('contractor_id')), $data)) {
                $data[$index] = new \stdClass();
                $data[$index]->contractorName = $item->offsetGet('contractor_name');
                $data[$index]->materialType = $item->offsetGet('material_type');
                $data[$index]->purchasePrice = 0;
                $data[$index]->purchaseWeight = 0;
                $data[$index]->warehousePrice = 0;
                $data[$index]->warehouseWeight = 0;
                $data[$index]->monthPrice = 0;
                $data[$index]->monthWeight = 0;
            }
            if (WarehouseLogEntity::DIRECTION_INPUT == $item->offsetGet('direction')) {
                $data[$index]->monthPrice = bcadd($data[$index]->monthPrice, $item->offsetGet('resource_price'), 4);
                $data[$index]->monthWeight = bcadd($data[$index]->monthWeight, $item->offsetGet('resource_weight'), 4);
            } elseif (WarehouseLogEntity::DIRECTION_OUTPUT == $item->offsetGet('direction')) {
                $data[$index]->monthPrice = bcsub($data[$index]->monthPrice, $item->offsetGet('resource_price'), 4);
                $data[$index]->monthWeight = bcsub($data[$index]->monthWeight, $item->offsetGet('resource_weight'), 4);
            }
        }

        return $data;
    }

    /**
     * @param $companyId
     * @param null $date
     * @return array
     * @throws \Exception
     */
    function getPurchaseMaterials($companyId, $date = null) {
        $sql = new Sql($this->db);
        $select = $sql->select(['a' => 'purchase_contracts']);
        $select->columns(['contractor_id' => 'provider_id']);
        $select->join(['b' => 'purchase_wagons'], 'a.contract_id = b.contract_id', ['material_price', 'loading_weight']);

        $select->join(['c' => 'materials'], 'a.material_id = c.material_id', ['material_id', 'material_name']);
        $select->join(['d' => 'material_types'], 'c.type_id = d.type_id', ['material_type' => 'name']);

        $select->join(['e' => 'contractors'], 'a.provider_id = e.contractor_id', ['contractor_name']);
        $select->where->equalTo('a.company_id', $companyId);
        $select->where->equalTo('e.contractor_type', ContractorAbstract::TYPE_PROVIDER);
        //$select->where->equalTo('b.status', PurchaseWagonEntity::STATUS_LOADED);
        if ($date instanceof \DateTime) {
            //$select->where->lessThanOrEqualTo('loading_date', $date->format('Y-m-d'));

            $select->where->nest()
                ->lessThan('b.loading_date', 'b.unloading_date', Where::TYPE_IDENTIFIER, Where::TYPE_IDENTIFIER)
                ->or
                ->isNull('b.unloading_date');
            $select->where->lessThanOrEqualTo('b.loading_date', $date->format('Y-m-d'));

        }

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new \Exception('Could not get the wagons with raw materials');

        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

//        echo "<pre>";
//        print_r($resultSet->toArray());
//        echo "</pre>";

        //echo '<pre>'; print_r($resultSet->toArray()); exit;


        $data = [];
        /** @var \ArrayObject $item */
        foreach ($resultSet as $item) {
            if (!key_exists(($index = 'i' . $item->offsetGet('contractor_id')), $data)) {
                $data[$index] = new \stdClass();
                $data[$index]->contractorName = $item->offsetGet('contractor_name');
                $data[$index]->materialType = $item->offsetGet('material_type');
                $data[$index]->purchasePrice = 0;
                $data[$index]->purchaseWeight = 0;
                $data[$index]->warehousePrice = 0;
                $data[$index]->warehouseWeight = 0;
                $data[$index]->monthPrice = 0;
                $data[$index]->monthWeight = 0;
            }

            $data[$index]->purchasePrice = bcadd($data[$index]->purchasePrice, $item->offsetGet('material_price'), 4);
            $data[$index]->purchaseWeight = bcadd($data[$index]->purchaseWeight, $item->offsetGet('loading_weight'), 4);
        }

        return $data;
    }

}