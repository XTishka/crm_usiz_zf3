<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCompanyManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorCompanyController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorCompany
     */
    protected $contractorCompanyForm;

    /**
     * @var ContractorCompanyManager
     */
    protected $contractorCompanyManager;

    /**
     * ContractorCompanyController constructor.
     * @param Form\ContractorCompany $contractorCompanyForm
     * @param ContractorCompanyManager $contractorCompanyManager
     */
    public function __construct(Form\ContractorCompany $contractorCompanyForm, ContractorCompanyManager $contractorCompanyManager) {
        $this->contractorCompanyForm = $contractorCompanyForm;
        $this->contractorCompanyManager = $contractorCompanyManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorCompanyManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorCompanyForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorCompanyManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorCompany/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorCompany');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorCompanyManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorCompanyManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorCompany');
    }

}