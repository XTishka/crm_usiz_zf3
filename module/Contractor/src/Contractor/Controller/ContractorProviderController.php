<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorProviderManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorProviderController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorProvider
     */
    protected $contractorProviderForm;

    /**
     * @var ContractorProviderManager
     */
    protected $contractorProviderManager;

    /**
     * ContractorProviderController constructor.
     * @param Form\ContractorProvider $contractorProviderForm
     * @param ContractorProviderManager $contractorProviderManager
     */
    public function __construct(Form\ContractorProvider $contractorProviderForm, ContractorProviderManager $contractorProviderManager) {
        $this->contractorProviderForm = $contractorProviderForm;
        $this->contractorProviderManager = $contractorProviderManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorProviderManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(100);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorProviderForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorProviderManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorProvider/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorProvider');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorProviderManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorProviderManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorProvider');
    }

}