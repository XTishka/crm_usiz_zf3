<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCorporateManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorCorporateController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorCorporate
     */
    protected $contractorCorporateForm;

    /**
     * @var ContractorCorporateManager
     */
    protected $contractorCorporateManager;

    /**
     * ContractorCorporateController constructor.
     * @param Form\ContractorCorporate $contractorCorporateForm
     * @param ContractorCorporateManager $contractorCorporateManager
     */
    public function __construct(Form\ContractorCorporate $contractorCorporateForm, ContractorCorporateManager $contractorCorporateManager) {
        $this->contractorCorporateForm = $contractorCorporateForm;
        $this->contractorCorporateManager = $contractorCorporateManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorCorporateManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorCorporateForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorCorporateManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorCorporate/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorCorporate');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorCorporateManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorCorporateManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorCorporate');
    }

}