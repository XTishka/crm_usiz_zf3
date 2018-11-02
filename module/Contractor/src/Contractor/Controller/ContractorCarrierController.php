<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCarrierManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorCarrierController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorCarrier
     */
    protected $contractorCarrierForm;

    /**
     * @var ContractorCarrierManager
     */
    protected $contractorCarrierManager;

    /**
     * ContractorCarrierController constructor.
     * @param Form\ContractorCarrier $contractorCarrierForm
     * @param ContractorCarrierManager $contractorCarrierManager
     */
    public function __construct(Form\ContractorCarrier $contractorCarrierForm, ContractorCarrierManager $contractorCarrierManager) {
        $this->contractorCarrierForm = $contractorCarrierForm;
        $this->contractorCarrierManager = $contractorCarrierManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorCarrierManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorCarrierForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorCarrierManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorCarrier/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorCarrier');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorCarrierManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorCarrierManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorCarrier');
    }

}