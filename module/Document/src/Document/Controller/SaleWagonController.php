<?php

namespace Document\Controller;

use Document\Form;
use Document\Service;
use Manufacturing\Service\WarehouseLogManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SaleWagonController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var Service\SaleContractManager
     */
    protected $saleContractManager;

    /**
     * @var Service\SaleWagonManager
     */
    protected $saleWagonManager;

    protected $saleEditWagonForm;

    /**
     * @var Form\SaleLoadingWagon
     */
    protected $saleLoadingWagon;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * SaleWagonController constructor.
     * @param Service\SaleContractManager $saleContractManager
     * @param Service\SaleWagonManager $saleWagonManager
     * @param Form\SaleEditWagon $saleEditWagonForm
     * @param Form\SaleLoadingWagon $saleLoadingWagon
     * @param WarehouseLogManager $warehouseLogManager
     */
    public function __construct(Service\SaleContractManager $saleContractManager,
                                Service\SaleWagonManager $saleWagonManager,
                                Form\SaleEditWagon $saleEditWagonForm,
                                Form\SaleLoadingWagon $saleLoadingWagon,
                                WarehouseLogManager $warehouseLogManager) {
        $this->saleContractManager = $saleContractManager;
        $this->saleWagonManager = $saleWagonManager;
        $this->saleEditWagonForm = $saleEditWagonForm;
        $this->saleLoadingWagon = $saleLoadingWagon;
        $this->warehouseLogManager = $warehouseLogManager;
    }

    public function editAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->saleContractManager->getContractById($contractId);
        $form = $this->saleEditWagonForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->saleWagonManager->saveWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('saleContract/advanced', ['id' => $contractId]);
            }
        } elseif ($saleWagonId = $this->params()->fromRoute('id')) {
            $object = $this->saleWagonManager->getWagonById($saleWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function loadingAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->saleContractManager->getContractById($contractId);
        $form = $this->saleLoadingWagon;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->saleWagonManager->loadWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('saleContract/advanced', ['id' => $contractId]);
            }
        } elseif ($saleWagonId = $this->params()->fromRoute('id')) {
            $object = $this->saleWagonManager->getWagonById($saleWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractId = $this->params()->fromRoute('contract');
        $saleWagonId = $this->params()->fromRoute('id');
        $result = $this->saleWagonManager->deleteWagonById($saleWagonId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('saleContract/advanced', ['id' => $contractId]);
    }


}