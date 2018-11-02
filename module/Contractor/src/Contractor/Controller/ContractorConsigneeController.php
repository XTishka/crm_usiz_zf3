<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorConsigneeManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorConsigneeController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorConsignee
     */
    protected $contractorConsigneeForm;

    /**
     * @var ContractorConsigneeManager
     */
    protected $contractorConsigneeManager;

    /**
     * ContractorConsigneeController constructor.
     * @param Form\ContractorConsignee $contractorConsigneeForm
     * @param ContractorConsigneeManager $contractorConsigneeManager
     */
    public function __construct(Form\ContractorConsignee $contractorConsigneeForm, ContractorConsigneeManager $contractorConsigneeManager) {
        $this->contractorConsigneeForm = $contractorConsigneeForm;
        $this->contractorConsigneeManager = $contractorConsigneeManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorConsigneeManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorConsigneeForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorConsigneeManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorConsignee/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorConsignee');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorConsigneeManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorConsigneeManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorConsignee');
    }

}