<?php

namespace Document\Controller;

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
     * @var SaleContractManager
     */
    protected $saleContractManager;

    /**
     * @var SaleWagonManager
     */
    protected $saleWagonManager;

    /**
     * @var Form\SaleContract
     */
    protected $saleContractForm;

    /**
     * SaleContractController constructor.
     * @param SaleContractManager $saleContractManager
     * @param SaleWagonManager $saleWagonManager
     * @param Form\SaleContract $saleContractForm
     */
    public function __construct(SaleContractManager $saleContractManager, SaleWagonManager $saleWagonManager, Form\SaleContract $saleContractForm) {
        $this->saleContractManager = $saleContractManager;
        $this->saleWagonManager = $saleWagonManager;
        $this->saleContractForm = $saleContractForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $companyId = $this->params()->fromRoute('id');
        $paginator = $this->saleContractManager->getContractsPaginator($companyId);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(100);

        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function advancedAction() {
        $contractId = $this->params()->fromRoute('id');
        $contract = $this->saleContractManager->getContractById($contractId);
        $wagons = $this->saleWagonManager->getWagonsPaginator($contractId);
        $wagons->setItemCountPerPage(100);
        $messenger = $this->plugin('FlashMessenger');
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('wagons', $wagons);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->saleContractForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->saleContractManager->saveContract($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('saleContract/edit', ['id' => $result->getSource()->getContractId()]);
                return $this->plugin('Redirect')->toRoute('saleContract');
            }
        } elseif ($saleContractId = $this->params()->fromRoute('id')) {
            $object = $this->saleContractManager->getContractById($saleContractId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $saleContractId = $this->params()->fromRoute('id');
        $result = $this->saleContractManager->deleteContractById($saleContractId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('saleContract');
    }

}