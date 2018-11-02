<?php

namespace Document\Controller;

use Document\Form;
use Document\Service;
use Manufacturing\Service\WarehouseLogManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PurchaseWagonController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var Service\PurchaseContractManager
     */
    protected $purchaseContractManager;

    /**
     * @var Service\PurchaseWagonManager
     */
    protected $purchaseWagonManager;

    protected $purchaseEditWagonForm;

    /**
     * @var Form\PurchaseLoadingWagon
     */
    protected $purchaseLoadingWagon;

    protected $purchaseUnloadingWagonForm;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * PurchaseWagonController constructor.
     * @param Service\PurchaseContractManager $purchaseContractManager
     * @param Service\PurchaseWagonManager $purchaseWagonManager
     * @param Form\PurchaseEditWagon $purchaseEditWagonForm
     * @param Form\PurchaseLoadingWagon $purchaseLoadingWagon
     * @param Form\PurchaseUnloadingWagon $purchaseUnloadingWagonForm
     * @param WarehouseLogManager $warehouseLogManager
     */
    public function __construct(Service\PurchaseContractManager $purchaseContractManager,
                                Service\PurchaseWagonManager $purchaseWagonManager,
                                Form\PurchaseEditWagon $purchaseEditWagonForm,
                                Form\PurchaseLoadingWagon $purchaseLoadingWagon,
                                Form\PurchaseUnloadingWagon $purchaseUnloadingWagonForm,
                                WarehouseLogManager $warehouseLogManager) {
        $this->purchaseContractManager = $purchaseContractManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->purchaseEditWagonForm = $purchaseEditWagonForm;
        $this->purchaseLoadingWagon = $purchaseLoadingWagon;
        $this->purchaseUnloadingWagonForm = $purchaseUnloadingWagonForm;
        $this->warehouseLogManager = $warehouseLogManager;
    }

    public function editAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $form = $this->purchaseEditWagonForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->purchaseWagonManager->saveWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('purchaseContract/advanced', ['id' => $contractId]);
            }
        } elseif ($purchaseWagonId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseWagonManager->getWagonById($purchaseWagonId);
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
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $form = $this->purchaseLoadingWagon;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->purchaseWagonManager->loadWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('purchaseContract/advanced', ['id' => $contractId]);
            }
        } elseif ($purchaseWagonId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseWagonManager->getWagonById($purchaseWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function unloadingAction() {
        $contractId = $this->params()->fromRoute('contract');
        $wagonId = $this->params()->fromRoute('id');
        $wagon = $this->purchaseWagonManager->getWagonById($wagonId);
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $form = $this->purchaseUnloadingWagonForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $wagon->setUnloadingDate(new \DateTime($data['unloading_date']));
                $wagon->setUnloadingWeight($data['unloading_weight']);
                $result = $this->purchaseWagonManager->unloadWagon($wagon);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('purchaseContract/advanced', ['id' => $contractId]);
            }
        } else {
            $form->populateValues([
                'wagon_id'         => $wagon->getWagonId(),
                'contract_id'      => $wagon->getContractId(),
                'unloading_weight' => $wagon->getLoadingWeight(),
            ]);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $contractId = $this->params()->fromRoute('contract');
        $purchaseWagonId = $this->params()->fromRoute('id');
        $result = $this->purchaseWagonManager->deleteWagonById($purchaseWagonId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('purchaseContract/advanced', ['id' => $contractId]);
    }


}