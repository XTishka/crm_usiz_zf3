<?php

namespace Manufacturing\Controller;

use Manufacturing\Form;
use Manufacturing\Service\WarehouseManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WarehouseController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var WarehouseManager
     */
    protected $warehouseManager;

    /**
     * @var Form\Warehouse
     */
    protected $warehouseForm;

    /**
     * WarehouseController constructor.
     * @param WarehouseManager $warehouseManager
     * @param Form\Warehouse $warehouseForm
     */
    public function __construct(WarehouseManager $warehouseManager, Form\Warehouse $warehouseForm) {
        $this->warehouseManager = $warehouseManager;
        $this->warehouseForm = $warehouseForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->warehouseManager->getWarehousesPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->warehouseForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->warehouseManager->saveWarehouse($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('warehouse/edit', ['id' => $result->getSource()->getWarehouseId()]);
                return $this->plugin('Redirect')->toRoute('warehouse');
            }
        } elseif ($warehouseId = $this->params()->fromRoute('id')) {
            $object = $this->warehouseManager->getWarehouseById($warehouseId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $warehouseId = $this->params()->fromRoute('id');
        $result = $this->warehouseManager->deleteWarehouseById($warehouseId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('warehouse');
    }

}