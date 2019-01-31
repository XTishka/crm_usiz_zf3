<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCommonManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorCommonController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorCommon
     */
    protected $contractorCommonForm;

    /**
     * @var ContractorCommonManager
     */
    protected $contractorCommonManager;

    /**
     * @var Form\ContractorCommonFilter
     */
    protected $contractorCommonFilterForm;

    /**
     * ContractorCommonController constructor.
     *
     * @param ContractorCommonManager     $contractorCommonManager
     * @param Form\ContractorCommonFilter $contractorCommonFilterForm
     */
    public function __construct(Form\ContractorCommonFilter $contractorCommonFilterForm, ContractorCommonManager $contractorCommonManager) {
        $this->contractorCommonManager    = $contractorCommonManager;
        $this->contractorCommonFilterForm = $contractorCommonFilterForm;
    }

    public function indexAction() {
        $queryParams = $this->params()->fromQuery();
        $filterForm  = $this->contractorCommonFilterForm;
        if (count($queryParams)) {
            $filterForm->populateValues($queryParams);
        }

        $messenger  = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator  = $this->contractorCommonManager->getAllContractorsWithPaginator($queryParams);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(100);


        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        $viewModel->setVariable('filterForm', $filterForm);
        return $viewModel;
    }

    public function editAction() {
        $form      = $this->contractorCommonForm;
        $messenger = $this->plugin('FlashMessenger');

        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorCommonManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain')) {
                    return $redirect->toRoute('contractorCommon/edit', ['id' => $result->getSource()->getContractorId()]);
                }
                return $redirect->toRoute('contractorCommon');
            }
        }
        elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorCommonManager->getContractorById($contractorId);
            $form->bind($object);
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result       = $this->contractorCommonManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorCommon');
    }

}