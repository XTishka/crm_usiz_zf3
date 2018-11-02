<?php

namespace Bank\Controller;

use Bank\Entity\RecordEntity;
use Bank\Exception\DeleteErrorException;
use Bank\Exception\NotFoundException;
use Bank\Exception\SaveErrorException;
use Bank\Form\RecordForm;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PlantRecordController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RecordForm
     */
    protected $recordForm;

    /**
     * @var RecordManager
     */
    protected $recordManager;

    /**
     * @var ContractorPlantManager
     */
    protected $plantManager;

    /**
     * PlantRecordController constructor.
     * @param RecordForm             $recordForm
     * @param RecordManager          $recordManager
     * @param ContractorPlantManager $plantManager
     */
    public function __construct(RecordForm $recordForm, RecordManager $recordManager, ContractorPlantManager $plantManager) {
        $this->recordForm = $recordForm;
        $this->recordManager = $recordManager;
        $this->plantManager = $plantManager;
    }

    /**
     * @return ViewModel
     * @throws NotFoundException
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $plantId = $this->params()->fromRoute('plant');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->recordManager->fetchAllWithPaginator($plantId);
        $paginator->setCurrentPageNumber($pageNumber);
        $plant = $this->plantManager->getContractorById($plantId);

        $data = $this->recordManager->getChessData($plantId);

        $view = new ViewModel();
        $view->setVariable('data', $data);
        $view->setVariable('plantId', $plantId);
        $view->setVariable('plant', $plant);
        $view->setVariable('paginator', $paginator);
        $view->setVariable('messenger', $this->plugin('flashMessenger'));
        return $view;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function editAction() {
        $plantId = $this->params()->fromRoute('plant');
        $form = $this->recordForm;
        if ($this->request->isPost()) {
            $dataFromPost = $this->params()->fromPost();
            $form->setData($dataFromPost);
            if ($form->isValid()) {
                /** @var RecordEntity $recordEntity */
                $recordEntity = $form->getObject();
                try {
                    $recordEntity = $this->recordManager->saveRecord($recordEntity);
                    $this->plugin('FlashMessenger')->addSuccessMessage('The record data was successfully saved');
                } catch (SaveErrorException $saveErrorException) {
                    $this->plugin('FlashMessenger')->addErrorMessage($saveErrorException->getMessage());
                }
                $this->redirect()->toRoute('plantDashboard/bank', ['plant' => $recordEntity->getCompanyId()]);
            }
        } elseif ($recordId = $this->params()->fromRoute('id')) {
            try {
                $recordEntity = $this->recordManager->fetchOneById($recordId);
                $form->bind($recordEntity);
            } catch (NotFoundException $notFoundException) {
                $this->plugin('FlashMessenger')->addErrorMessage($notFoundException->getMessage());
                $this->redirect()->toRoute('plantDashboard/bank', ['plant' => $plantId]);
            }
        } else {
            $form->get('company_id')->setValue($plantId);
            $form->get('bank_id')->setValue($this->params()->fromQuery('bank'));
            $form->get('date')->setValue($this->params()->fromQuery('date'));
        }

        $plant = $this->plantManager->getContractorById($plantId);

        $view = new ViewModel();
        $view->setVariable('plantId', $plantId);
        $view->setVariable('plant', $plant);
        $view->setVariable('messenger', $this->plugin('flashMessenger'));
        $view->setVariable('form', $form);
        return $view;
    }

    public function deleteAction() {
        $plantId = $this->params()->fromRoute('plant');
        $recordId = $this->params()->fromRoute('id');
        try {
            $this->recordManager->deleteRecord($recordId);
            $this->plugin('FlashMessenger')->addSuccessMessage('Record data has been successfully deleted');
        } catch (DeleteErrorException $deleteErrorException) {
            $this->plugin('FlashMessenger')->addErrorMessage($deleteErrorException->getMessage());
        }
        $this->redirect()->toRoute('plantDashboard/bank', ['plant' => $plantId]);
    }

}