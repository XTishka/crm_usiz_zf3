<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorAdditionalManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorAdditionalController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorAdditional
     */
    protected $contractorAdditionalForm;

    /**
     * @var ContractorAdditionalManager
     */
    protected $contractorAdditionalManager;

    /**
     * ContractorAdditionalController constructor.
     * @param Form\ContractorAdditional $contractorAdditionalForm
     * @param ContractorAdditionalManager $contractorAdditionalManager
     */
    public function __construct(Form\ContractorAdditional $contractorAdditionalForm, ContractorAdditionalManager $contractorAdditionalManager) {
        $this->contractorAdditionalForm = $contractorAdditionalForm;
        $this->contractorAdditionalManager = $contractorAdditionalManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorAdditionalManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorAdditionalForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorAdditionalManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorAdditional/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorAdditional');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorAdditionalManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorAdditionalManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorAdditional');
    }

}