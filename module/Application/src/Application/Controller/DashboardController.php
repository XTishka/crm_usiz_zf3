<?php

namespace Application\Controller;

use Application\Model\CheckingAccountService;
use Application\Model\Finance\AccountPayableService;
use Application\Model\Finance\CharterCapitalService;
use Application\Model\Finance\CustomerReceivableService;
use Application\Model\Finance\DebtToCarrierService;
use Application\Model\Finance\DebtToOtherService;
use Application\Model\Finance\DebtToPlantService;
use Application\Model\Finance\DebtToProviderService;
use Application\Model\Finance\PrepayFromCustomerService;
use Application\Model\Finance\PrepayToCarrierService;
use Application\Model\Finance\PrepayToOtherService;
use Application\Model\Finance\PrepayToPlantService;
use Application\Model\Finance\PrepayToProviderService;
use Application\Model\Finance\TotalReceivableService;
use Bank\Service\RecordManager;
use Contractor\Entity\ContractorCompany;
use Contractor\Entity\ContractorPlant;
use Contractor\Service\ContractorCompanyManager;
use Document\Service\FinanceManager;
use Document\Service\PurchaseWagonManager;
use Manufacturing\Service\SkipManager;
use Manufacturing\Service\WarehouseLogManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use DateTime;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var CheckingAccountService
     */
    protected $checkingAccountService;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var FinanceManager
     */
    protected $financeManager;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * @var SkipManager
     */
    protected $furnaceSkipManager;

    /**
     * @var PurchaseWagonManager
     */
    protected $purchaseWagonManager;

    /**
     * @var RecordManager
     */
    protected $recordManager;

    /**
     * @var CustomerReceivableService
     */
    protected $customerReceivableService;

    /**
     * @var PrepayToProviderService
     */
    protected $prepayToProviderService;

    /**
     * @var PrepayToCarrierService
     */
    protected $prepayToCarrierService;

    /**
     * @var PrepayToPlantService
     */
    protected $prepayToPlantService;

    /**
     * @var PrepayToOtherService
     */
    protected $prepayToOtherService;

    /**
     * @var TotalReceivableService
     */
    protected $totalReceivableService;

    /**
     * @var CharterCapitalService
     */
    protected $charterCapitalService;

    /**
     * @var PrepayFromCustomerService
     */
    protected $prepayFromCustomerService;

    /**
     * @var DebtToCarrierService
     */
    protected $debtToCarrierService;

    /**
     * @var DebtToProviderService
     */
    protected $debtToProviderService;

    /**
     * @var DebtToPlantService
     */
    protected $debtToPlantService;

    /**
     * @var DebtToOtherService
     */
    protected $debtToOtherService;

    /**
     * @var AccountPayableService
     */
    protected $accountPayableService;

    /**
     * @var \Bank\Service\RecordManager
     */
    protected $bankRecordManager;

    public function __construct(
        CheckingAccountService $checkingAccountService,
        ContractorCompanyManager $companyManager,
        WarehouseLogManager $warehouseLogManager,
        SkipManager $furnaceSkipManager,
        PurchaseWagonManager $purchaseWagonManager,
        RecordManager $recordManager,

        CustomerReceivableService $customerReceivableService,
        PrepayToProviderService $prepayToProviderService,
        PrepayToCarrierService $prepayToCarrierService,
        PrepayToPlantService $prepayToPlantService,
        PrepayToOtherService $prepayToOtherService,
        TotalReceivableService $totalReceivableService,

        CharterCapitalService $charterCapitalService,
        PrepayFromCustomerService $prepayFromCustomerService,
        DebtToCarrierService $debtToCarrierService,
        DebtToProviderService $debtToProviderService,
        DebtToPlantService $debtToPlantService,
        DebtToOtherService $debtToOtherService,
        AccountPayableService $accountPayableService,

        RecordManager $bankRecordManager
    ) {


        $this->checkingAccountService = $checkingAccountService;

        $this->companyManager = $companyManager;
        //$this->financeManager = $financeManager;
        $this->warehouseLogManager  = $warehouseLogManager;
        $this->furnaceSkipManager   = $furnaceSkipManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->recordManager        = $recordManager;

        $this->customerReceivableService = $customerReceivableService;
        $this->prepayToProviderService   = $prepayToProviderService;
        $this->prepayToCarrierService    = $prepayToCarrierService;
        $this->prepayToPlantService      = $prepayToPlantService;
        $this->prepayToOtherService      = $prepayToOtherService;
        $this->totalReceivableService    = $totalReceivableService;

        $this->charterCapitalService     = $charterCapitalService;
        $this->prepayFromCustomerService = $prepayFromCustomerService;
        $this->debtToCarrierService      = $debtToCarrierService;
        $this->debtToProviderService     = $debtToProviderService;
        $this->debtToPlantService        = $debtToPlantService;
        $this->debtToOtherService        = $debtToOtherService;
        $this->accountPayableService     = $accountPayableService;

        $this->bankRecordManager = $bankRecordManager;
    }

    public
    function welcomeAction() {
        $messenger = $this->plugin('FlashMessenger');
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     * @throws \Manufacturing\Exception\ErrorException
     */
    public
    function indexAction() {
        $companyId = $this->params()->fromRoute('company');

        /** @var ContractorCompany $company */
        $company = $this->companyManager->getContractorById($companyId);

        if ($company instanceof ContractorPlant) {
            $this->redirect()->toRoute('dashboard/finance', ['company' => $companyId]);
        }

        //$start = microtime(true); $times = [];

        $expectedWagons = $this->purchaseWagonManager->getExpectedWagonsStatistic($companyId);
        //$times['expectedWagons'] = (microtime(true) - $start).' сек.';

        $expectedMaterials = $this->purchaseWagonManager->getExpectedMaterialWeight($companyId);
        //$times['expectedMaterials'] = (microtime(true) - $start).' сек.';

        $totalMaterials = $this->purchaseWagonManager->getTotalMaterialWeight($company->getPlantId(), $company->getContractorId());
        //$times['totalMaterials'] = (microtime(true) - $start).' сек.';

        $messenger = $this->plugin('FlashMessenger');

        $materials = $this->warehouseLogManager->getMaterialBalances($company->getPlantId());
        $products  = $this->warehouseLogManager->getProductBalances($company->getPlantId());

        $furnaces = $this->furnaceSkipManager->getFurnaceLogByPlantId($company->getPlantId());

        $furnaceMaterials = $this->furnaceSkipManager->getFurnaceDataByPlantId($company->getPlantId());

        //echo '<pre>'; print_r($times); echo '</pre>'; echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';

        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);

        $viewModel->setVariable('materials', $materials);
        $viewModel->setVariable('products', $products);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('furnaces', $furnaces);
        $viewModel->setVariable('furnaceMaterials', $furnaceMaterials);
        $viewModel->setVariable('companyId', $companyId);
        $viewModel->setVariable('expectedMaterials', $expectedMaterials);
        $viewModel->setVariable('expectedWagons', $expectedWagons);
        $viewModel->setVariable('totalMaterials', $totalMaterials);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function financeAction() {
        $companyId = $this->params()->fromRoute('company');
        $company   = $this->companyManager->getContractorById($companyId);
        $messenger = $this->plugin('FlashMessenger');

        if ($date = $this->params()->fromQuery('date')) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
            $dateSub = clone $date;
            $dateSub->sub(new \DateInterval('P1D'));

            $this->customerReceivableService->setDate($dateSub);
            $this->prepayToProviderService->setDate($dateSub);
            $this->prepayToCarrierService->setDate($dateSub);
            $this->prepayToPlantService->setDate($dateSub);
            $this->prepayToOtherService->setDate($dateSub);
            $this->totalReceivableService->setDate($dateSub);

            $this->charterCapitalService->setDate($dateSub);
            $this->prepayFromCustomerService->setDate($dateSub);
            $this->debtToCarrierService->setDate($dateSub);
            $this->debtToProviderService->setDate($dateSub);
            $this->debtToPlantService->setDate($dateSub);
            $this->debtToOtherService->setDate($dateSub);
            $this->accountPayableService->setDate($dateSub);
        }
        else {
            $dateSub = new DateTime();
            $dateSub->sub(new \DateInterval('P1D'));
        }


        $bankAmountRowset = $this->bankRecordManager->getBankAmountRowset($companyId, $dateSub);

        $bankTotalSum = 0;
        foreach ($bankAmountRowset as $amount) {
            $bankTotalSum = bcadd($bankTotalSum, $amount->offsetGet('amount'), 4);
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('warehouseBalance', $this->warehouseLogManager->getTotalMaterialBalances($company->getPlantId(), $dateSub));
        $viewModel->setVariable('expectedMaterials', $this->purchaseWagonManager->getExpectedMaterialWeight($companyId, $dateSub));
        $viewModel->setVariable('checkingAccount', $this->checkingAccountService->getRecords($companyId, $dateSub));

        $viewModel->setVariable('bankTotalSum', $bankTotalSum);
        /* -------------------------- */

        $viewModel->setVariable('customerReceivableContainer', $this->customerReceivableService->getRecords($companyId));
        $viewModel->setVariable('prepayToProviderContainer', $this->prepayToProviderService->getRecords($companyId));
        $viewModel->setVariable('prepayToCarrierContainer', $this->prepayToCarrierService->getRecords($companyId));
        $viewModel->setVariable('prepayToPlantContainer', $this->prepayToPlantService->getRecords($companyId));
        $viewModel->setVariable('prepayToOtherContainer', $this->prepayToOtherService->getRecords($companyId));
        $viewModel->setVariable('totalReceivableContainer', $this->totalReceivableService->getRecords($companyId));

        $viewModel->setVariable('charterCapitalContainer', $this->charterCapitalService->getRecords($companyId));
        $viewModel->setVariable('prepayFromCustomerContainer', $this->prepayFromCustomerService->getRecords($companyId));
        $viewModel->setVariable('debtToCarrierContainer', $this->debtToCarrierService->getRecords($companyId));
        $viewModel->setVariable('debtToProviderContainer', $this->debtToProviderService->getRecords($companyId));
        $viewModel->setVariable('debtToPlantContainer', $this->debtToPlantService->getRecords($companyId));
        $viewModel->setVariable('debtToOtherContainer', $this->debtToOtherService->getRecords($companyId));
        $viewModel->setVariable('accountPayableContainer', $this->accountPayableService->getRecords($companyId));


        $viewModel->setVariable('date', $date ? $date->format('d.m.Y') : null);
        $viewModel->setVariable('plantId', $companyId);
        return $viewModel;
    }

}