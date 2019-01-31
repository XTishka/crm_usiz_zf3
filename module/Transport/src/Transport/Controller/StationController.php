<?php

namespace Transport\Controller;

use Transport\Form;
use Transport\Service\StationManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class StationController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var StationManager
     */
    protected $stationManager;

    /**
     * @var Form\Station
     */
    protected $stationForm;

    /**
     * StationController constructor.
     * @param StationManager $stationManager
     * @param Form\Station $stationForm
     */
    public function __construct(StationManager $stationManager, Form\Station $stationForm) {
        $this->stationManager = $stationManager;
        $this->stationForm = $stationForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->stationManager->getStationsPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->stationForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->stationManager->saveStation($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('station/edit', ['id' => $result->getSource()->getStationId()]);
                return $this->plugin('Redirect')->toRoute('station');
            }
        } elseif ($stationId = $this->params()->fromRoute('id')) {
            $object = $this->stationManager->getStationById($stationId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $stationId = $this->params()->fromRoute('id');
        $result = $this->stationManager->deleteStationById($stationId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('station');
    }

}