<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorPlantManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ContractorPlantController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Form\ContractorPlant
     */
    protected $contractorPlantForm;

    /**
     * @var ContractorPlantManager
     */
    protected $contractorPlantManager;

    /**
     * ContractorPlantController constructor.
     * @param Form\ContractorPlant $contractorPlantForm
     * @param ContractorPlantManager $contractorPlantManager
     */
    public function __construct(Form\ContractorPlant $contractorPlantForm, ContractorPlantManager $contractorPlantManager) {
        $this->contractorPlantForm = $contractorPlantForm;
        $this->contractorPlantManager = $contractorPlantManager;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->contractorPlantManager->getContractorsWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->contractorPlantForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->contractorPlantManager->saveContractor($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                $redirect = $this->plugin('Redirect');
                if ($this->params()->fromPost('save_end_remain'))
                    return $redirect->toRoute('contractorPlant/edit', ['id' => $result->getSource()->getContractorId()]);
                return $redirect->toRoute('contractorPlant');
            }
        } elseif ($contractorId = $this->params()->fromRoute('id')) {
            $object = $this->contractorPlantManager->getContractorById($contractorId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractorId = $this->params()->fromRoute('id');
        $result = $this->contractorPlantManager->deleteContractorById($contractorId);
        $this->plugin('FlashMessenger')->addMessage($result->getStatus(), $result->getMessage());
        return $this->plugin('Redirect')->toRoute('contractorPlant');
    }

}