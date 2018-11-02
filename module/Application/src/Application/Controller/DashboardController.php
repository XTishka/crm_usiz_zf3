<?php

namespace Application\Controller;

use Application\Model\AccountsPayableService;
use Application\Model\AccountsReceivableService;
use Application\Model\CarriersReceivableService;
use Application\Model\CheckingAccountService;
use Application\Model\CustomerPayableService;
use Application\Model\ProvidersReceivableService;
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
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

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

    /** @var AccountsPayableService */
    protected $accountsPayableService;

    /** @var AccountsReceivableService */
    protected $accountsReceivableService;

    /** @var CarriersReceivableService */
    protected $carriersReceivableService;

    /**
     * @var CheckingAccountService
     */
    protected $checkingAccountService;

    /**
     * @var CustomerPayableService
     */
    protected $customerPayableService;

    /** @var ProvidersReceivableService */
    protected $providersReceivableService;

    public function __construct(
        AccountsPayableService $accountsPayableService,
        AccountsReceivableService $accountsReceivableService,
        CarriersReceivableService $carriersReceivableService,
        CheckingAccountService $checkingAccountService,
        CustomerPayableService $customerPayableService,
        ProvidersReceivableService $providersReceivableService,
        ContractorCompanyManager $companyManager,
        FinanceManager $financeManager,
        WarehouseLogManager $warehouseLogManager,
        SkipManager $furnaceSkipManager,
        PurchaseWagonManager $purchaseWagonManager,
        RecordManager $recordManager) {

        $this->accountsPayableService = $accountsPayableService;
        $this->accountsReceivableService = $accountsReceivableService;
        $this->carriersReceivableService = $carriersReceivableService;
        $this->checkingAccountService = $checkingAccountService;
        $this->customerPayableService = $customerPayableService;
        $this->providersReceivableService = $providersReceivableService;

        $this->companyManager = $companyManager;
        $this->financeManager = $financeManager;
        $this->warehouseLogManager = $warehouseLogManager;
        $this->furnaceSkipManager = $furnaceSkipManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->recordManager = $recordManager;
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
        $products = $this->warehouseLogManager->getProductBalances($company->getPlantId());

        $furnaces = $this->furnaceSkipManager->getFurnaceLogByPlantId($company->getPlantId());

        //echo '<pre>'; print_r($times); echo '</pre>'; echo 'Время выполнения скрипта: '.(microtime(true) - $start).' сек.';

        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);

        $viewModel->setVariable('materials', $materials);
        $viewModel->setVariable('products', $products);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('furnaces', $furnaces);
        $viewModel->setVariable('companyId', $companyId);
        $viewModel->setVariable('expectedMaterials', $expectedMaterials);
        $viewModel->setVariable('expectedWagons', $expectedWagons);
        $viewModel->setVariable('totalMaterials', $totalMaterials);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Bank\Exception\NotFoundException
     * @throws \Contractor\Exception\ErrorException
     * @throws \Document\Exception\ErrorException
     * @throws \Manufacturing\Exception\ErrorException
     */
    public
    function financeAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $messenger = $this->plugin('FlashMessenger');

        if ($date = $this->params()->fromQuery('date')) {
            $date = \DateTime::createFromFormat('d.m.Y', $date);
        } else {
            $date = null;
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('warehouseBalance', $this->warehouseLogManager->getTotalMaterialBalances($company->getPlantId(), $date));
        $viewModel->setVariable('expectedMaterials', $this->purchaseWagonManager->getExpectedMaterialWeight($companyId, $date));
        //$viewModel->setVariable('companyBalance', $this->financeManager->getCompanyBalance($companyId, $date));
        //$viewModel->setVariable('companyPrepaymentSum', $this->financeManager->getCompanyPrepaymentSum($companyId, $date));
        $viewModel->setVariable('customerPrepaymentSum', $this->financeManager->getCustomerPrepaymentSum($companyId, $date));
        $viewModel->setVariable('internalPayableSum', $this->financeManager->getInternalPayableSum($companyId, $date));
        $viewModel->setVariable('bankTotalSum', $this->recordManager->getCurrentTotalAmount($companyId));

        /* -------------------------- */
        $viewModel->setVariable('accountsPayable', $this->accountsPayableService->getRecords($companyId, $date));
        $viewModel->setVariable('accountsReceivable', $this->accountsReceivableService->getRecords($companyId, $date));
        $viewModel->setVariable('carriersReceivable', $this->carriersReceivableService->getRecords($companyId, $date));
        $viewModel->setVariable('checkingAccount', $this->checkingAccountService->getRecords($companyId, $date));
        $viewModel->setVariable('customerPayable', $this->customerPayableService->getRecords($companyId, $date));
        $viewModel->setVariable('providersReceivable', $this->providersReceivableService->getRecords($companyId, $date));
        /* -------------------------- */

        $viewModel->setVariable('companyToPlantPrepaymentSum', $this->financeManager->getCompanyToPlantPrepaymentSum($companyId, $date));


        $viewModel->setVariable('date', $date ? $date->format('d.m.Y') : null);
        $viewModel->setVariable('plantId', $companyId);
        return $viewModel;
    }

}