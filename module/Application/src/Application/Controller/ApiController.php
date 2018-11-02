<?php

namespace Application\Controller;

use Application\Model\AccountsPayableService;
use Application\Model\AccountsReceivableService;
use Application\Model\CarriersReceivableService;
use Application\Model\CheckingAccountService;
use Application\Model\CustomerPayableService;
use Application\Model\ProvidersReceivableService;
use Application\Service\Repository\ApiDb;
use Contractor\Entity\ContractorCompany;
use Contractor\Service\ContractorCompanyManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ApiController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ApiDb
     */
    protected $apiDbRepository;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var AccountsPayableService
     */
    protected $accountsPayableService;

    /**
     * @var AccountsReceivableService
     */
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

    /**
     * @var ProvidersReceivableService
     */
    protected $providersReceivableService;

    public function __construct(
        AccountsPayableService $accountsPayableService,
        AccountsReceivableService $accountsReceivableService,
        CarriersReceivableService $carriersReceivableService,
        CheckingAccountService $checkingAccountService,
        CustomerPayableService $customerPayableService,
        ProvidersReceivableService $providersReceivableService,
        ApiDb $apiDbRepository, ContractorCompanyManager $companyManager) {

        $this->accountsPayableService = $accountsPayableService;
        $this->accountsReceivableService = $accountsReceivableService;
        $this->carriersReceivableService = $carriersReceivableService;
        $this->checkingAccountService = $checkingAccountService;
        $this->customerPayableService = $customerPayableService;
        $this->providersReceivableService = $providersReceivableService;

        $this->apiDbRepository = $apiDbRepository;
        $this->companyManager = $companyManager;
    }

    public function bankBalancesAction() {
        $companyId = $this->params()->fromRoute('company');
        $paginator = $this->apiDbRepository->getBankBalancesPaginator($companyId);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Application\Exception\ErrorException
     * @throws \Contractor\Exception\ErrorException
     */
    public function materialAssetsAction() {
        $companyId = $this->params()->fromRoute('company');
        $materialId = $this->params()->fromRoute('material');
        $date = $this->params()->fromQuery('date');

        /** @var ContractorCompany $company */
        $company = $this->companyManager->getContractorById($companyId);

        $warehouseData = $this->apiDbRepository->getMaterialWarehouseBalances($company->getPlantId(), $materialId, $date);
        $expectedData = $this->apiDbRepository->getExpectedMaterialBalances($companyId, $materialId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('expectedData', $expectedData);
        $viewModel->setVariable('warehouseData', $warehouseData);
        return $viewModel;
    }

    public function companyDebitsAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        //$paginator = $this->apiDbRepository->getCompanyDebitOperationsPaginator($companyId, $date);
        $paginator = $this->checkingAccountService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function customerDebtsAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        //$paginator = $this->apiDbRepository->getCustomerDebtsPaginator($companyId, $date);
        $paginator = $this->accountsReceivableService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function plantDebtsAction() {
        $companyId = $this->params()->fromRoute('company');
        $paginator = $this->apiDbRepository->getPlantDebtsPaginator($companyId);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function companyPrepaymentsAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        //$paginator = $this->apiDbRepository->getCompanyPrepaymentOperationsPaginator($companyId, $date);
        $paginator = $this->providersReceivableService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function plantPrepaymentsAction() {
        $plantId = $this->params()->fromRoute('plant');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->apiDbRepository->getPlantPrepaymentOperationsPaginator($plantId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function companyToPlantPrepaymentsAction() {
        $plantId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->apiDbRepository->getCompanyToPlantPrepaymentOperationsPaginator($plantId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function carriersReceivableAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->carriersReceivableService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function plantFromCompanyPrepaymentsAction() {
        $plantId = $this->params()->fromRoute('plant');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->apiDbRepository->getPlantFromCompanyPrepaymentOperationsPaginator($plantId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function companyCorporatesAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->apiDbRepository->getCompanyCorporateOperationsPaginator($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function customerPrepaymentsAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        //$paginator = $this->apiDbRepository->getCustomerPrepaymentOperationsPaginator($companyId, $date);
        $paginator = $this->customerPayableService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function companyPayablesAction() {
        $companyId = $this->params()->fromRoute('company');
        $date = $this->params()->fromQuery('date');
        $paginator = $this->accountsPayableService->getRecords($companyId, $date);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function providerTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $providerId = $this->params()->fromRoute('contractor');
        $paginator = $this->apiDbRepository->getProviderTransactionsPaginator($companyId, $providerId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function customerTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $customerId = $this->params()->fromRoute('contractor');
        $paginator = $this->apiDbRepository->getCustomerTransactionsPaginator($companyId, $customerId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function carrierTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $carrierId = $this->params()->fromRoute('contractor');
        $paginator = $this->apiDbRepository->getCarrierTransactionsPaginator($companyId, $carrierId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function additionalTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $additionalId = $this->params()->fromRoute('contractor');
        $paginator = $this->apiDbRepository->getAdditionalTransactionsPaginator($companyId, $additionalId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function corporateTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $corporateId = $this->params()->fromRoute('contractor');
        $paginator = $this->apiDbRepository->getCorporateTransactionsPaginator($companyId, $corporateId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function plantTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $plantId = $this->params()->fromRoute('plant');
        $paginator = $this->apiDbRepository->getPlantTransactionsPaginator($companyId, $plantId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function companyTransactionsAction() {
        $companyId = $this->params()->fromRoute('company');
        $plantId = $this->params()->fromRoute('plant');
        $paginator = $this->apiDbRepository->getCompanyTransactionsPaginator($plantId, $companyId);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

}