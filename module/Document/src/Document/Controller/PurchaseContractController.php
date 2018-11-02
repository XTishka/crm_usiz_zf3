<?php

namespace Document\Controller;

use Document\Form;
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
     * @var PurchaseContractManager
     */
    protected $purchaseContractManager;

    /**
     * @var PurchaseWagonManager
     */
    protected $purchaseWagonManager;

    /**
     * @var Form\PurchaseContract
     */
    protected $purchaseContractForm;

    /**
     * PurchaseContractController constructor.
     * @param PurchaseContractManager $purchaseContractManager
     * @param PurchaseWagonManager $purchaseWagonManager
     * @param Form\PurchaseContract $purchaseContractForm
     */
    public function __construct(PurchaseContractManager $purchaseContractManager,
                                PurchaseWagonManager $purchaseWagonManager,
                                Form\PurchaseContract $purchaseContractForm) {
        $this->purchaseContractManager = $purchaseContractManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->purchaseContractForm = $purchaseContractForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $companyId = $this->params()->fromRoute('id');
        $paginator = $this->purchaseContractManager->getContractsPaginator($companyId);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        $viewModel->setVariable('companyId', $companyId);
        return $viewModel;
    }

    public function advancedAction() {
        $contractId = $this->params()->fromRoute('id');
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $wagons = $this->purchaseWagonManager->getWagonsPaginator($contractId);
        $wagons->setItemCountPerPage(100);
        $messenger = $this->plugin('FlashMessenger');
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('wagons', $wagons);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->purchaseContractForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->purchaseContractManager->saveContract($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('purchaseContract/edit', ['id' => $result->getSource()->getContractId()]);
                return $this->plugin('Redirect')->toRoute('purchaseContract');
            }
        } elseif ($purchaseContractId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseContractManager->getContractById($purchaseContractId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $purchaseContractId = $this->params()->fromRoute('id');
        $result = $this->purchaseContractManager->deleteContractById($purchaseContractId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('purchaseContract');
    }

}