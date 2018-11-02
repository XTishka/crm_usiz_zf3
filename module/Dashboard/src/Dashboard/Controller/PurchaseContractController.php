<?php

namespace Dashboard\Controller;

use Application\Model\PurchaseWagonsService;
use Application\Model\PurchaseWagonsServiceFactory;
use Contractor\Service\ContractorCompanyManager;
use Document\Form\PurchaseContract;
use Document\Form\PurchaseImportWagon;
use Document\Form\PurchaseWagonFilter;
use Document\Service\PurchaseContractManager;
use Document\Service\PurchaseWagonManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PurchaseContractController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var PurchaseContractManager
     */
    protected $purchaseContractManager;

    /**
     * @var PurchaseWagonManager
     */
    protected $purchaseWagonManager;


    protected $purchaseWagonsService;

    /**
     * @var PurchaseContract
     */
    protected $purchaseContractForm;

    protected $purchaseWagonFilterForm;

    /**
     * PurchaseContractController constructor.
     * @param ContractorCompanyManager $companyManager
     * @param PurchaseContractManager $purchaseContractManager
     * @param PurchaseWagonManager $purchaseWagonManager
     * @param PurchaseWagonsService $purchaseWagonsService
     * @param PurchaseContract $purchaseContractForm
     * @param PurchaseWagonFilter $purchaseWagonFilterForm
     */
    public function __construct(
        ContractorCompanyManager $companyManager,
        PurchaseContractManager $purchaseContractManager,
        PurchaseWagonManager $purchaseWagonManager,
        PurchaseWagonsService $purchaseWagonsService,
        PurchaseContract $purchaseContractForm,
        PurchaseWagonFilter $purchaseWagonFilterForm) {
        $this->companyManager = $companyManager;
        $this->purchaseContractManager = $purchaseContractManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->purchaseWagonsService = $purchaseWagonsService;
        $this->purchaseContractForm = $purchaseContractForm;
        $this->purchaseWagonFilterForm = $purchaseWagonFilterForm;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $paginator = $this->purchaseContractManager->getContractsPaginator($companyId);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        $viewModel->setVariable('company', $company);
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
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $filterData = $this->params()->fromQuery();
        $filterForm = $this->purchaseWagonFilterForm;
        $filterForm->populateValues($filterData);
        $wagons = $this->purchaseWagonsService->getWagons($contractId, $filterData);
        $messenger = $this->plugin('FlashMessenger');
        $viewModel = new ViewModel();
        $viewModel->setVariable('filterForm', $filterForm);
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('company', $company);
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
        $form = $this->purchaseContractForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->purchaseContractManager->saveContract($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/edit', ['id' => $result->getSource()->getContractId(), 'company' => $companyId]);
                return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract', ['company' => $companyId]);
            }
        } elseif ($purchaseContractId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseContractManager->getContractById($purchaseContractId);
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
        $purchaseContractId = $this->params()->fromRoute('id');
        $result = $this->purchaseContractManager->deleteContractById($purchaseContractId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('dashboard/purchaseContract', ['company' => $companyId]);
    }


}