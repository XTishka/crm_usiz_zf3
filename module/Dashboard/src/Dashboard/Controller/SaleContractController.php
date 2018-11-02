<?php

namespace Dashboard\Controller;

use Application\Model\SaleWagonsService;
use Contractor\Service\ContractorCompanyManager;
use Document\Form;
use Document\Service\SaleContractManager;
use Document\Service\SaleWagonManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SaleContractController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var SaleContractManager
     */
    protected $saleContractManager;

    /**
     * @var SaleWagonManager
     */
    protected $saleWagonManager;

    /**
     * @var SaleWagonsService
     */
    protected $saleWagonsService;

    /**
     * @var Form\SaleContract
     */
    protected $saleContractForm;

    /**
     * @var Form\SaleWagonFilter
     */
    protected $saleWagonFilterForm;

    /**
     * SaleContractController constructor.
     * @param ContractorCompanyManager $companyManager
     * @param SaleContractManager $saleContractManager
     * @param SaleWagonManager $saleWagonManager
     * @param SaleWagonsService $saleWagonsService
     * @param Form\SaleContract $saleContractForm
     * @param Form\SaleWagonFilter $saleWagonFilter
     */
    public function __construct(ContractorCompanyManager $companyManager,
                                SaleContractManager $saleContractManager,
                                SaleWagonManager $saleWagonManager,
                                SaleWagonsService $saleWagonsService,
                                Form\SaleContract $saleContractForm,
                                Form\SaleWagonFilter $saleWagonFilter) {
        $this->companyManager = $companyManager;
        $this->saleContractManager = $saleContractManager;
        $this->saleWagonManager = $saleWagonManager;
        $this->saleWagonsService = $saleWagonsService;
        $this->saleContractForm = $saleContractForm;
        $this->saleWagonFilterForm = $saleWagonFilter;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->saleContractManager->getContractsPaginator($companyId);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function advancedAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $contractId = $this->params()->fromRoute('id');
        $contract = $this->saleContractManager->getContractById($contractId);

        $filterData = $this->params()->fromQuery();
        $filterForm = $this->saleWagonFilterForm;
        $filterForm->populateValues($filterData);

        $wagons = $this->saleWagonsService->getWagons($contractId, $filterData);
        $messenger = $this->plugin('FlashMessenger');

        $viewModel = new ViewModel();
        $viewModel->setVariable('filterForm', $filterForm);
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('wagons', $wagons);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function editAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $form = $this->saleContractForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->saleContractManager->saveContract($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('dashboard/saleContract/edit', [
                        'company' => $company->getContractorId(),
                        'id'      => $result->getSource()->getContractId()]);
                return $this->plugin('Redirect')->toRoute('dashboard/saleContract', [
                    'company' => $company->getContractorId()]);
            }
        } else if ($saleContractId = $this->params()->fromRoute('id')) {
            $object = $this->saleContractManager->getContractById($saleContractId);
            $form->bind($object);
        } else {
            $form->populateValues([
                'company_id' => $companyId,
            ]);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $companyId = $this->params()->fromRoute('company');
        $saleContractId = $this->params()->fromRoute('id');
        $result = $this->saleContractManager->deleteContractById($saleContractId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('dashboard/saleContract', ['company' => $companyId]);
    }

}