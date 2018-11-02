<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCustomerManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorCustomerController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorCustomer
     */
    protected $contractorCustomerForm;

    /**
     * @var ContractorCustomerManager
     */
    protected $contractorCustomerManager;

    /**
     * ContractorCustomerController constructor.
     * @param Form\ContractorCustomer $contractorCustomerForm
     * @param ContractorCustomerManager $contractorCustomerManager
     */
    public function __construct(Form\ContractorCustomer $contractorCustomerForm, ContractorCustomerManager $contractorCustomerManager) {
        $this->contractorCustomerForm = $contractorCustomerForm;
        $this->contractorCustomerManager = $contractorCustomerManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorCustomerManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorCustomerForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorCustomerManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorCustomer/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorCustomer');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorCustomerManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorCustomerManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorCustomer');
    }

}