<?php

namespace Dashboard\Controller;

use Contractor\Service\ContractorCompanyManager;
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
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * @var Service\PurchaseContractManager
     */
    protected $purchaseContractManager;

    /**
     * @var Service\PurchaseWagonManager
     */
    protected $purchaseWagonManager;

    /**
     * @var Form\PurchaseEditWagon
     */
    protected $purchaseEditWagonForm;

    /**
     * @var Form\PurchaseLoadingWagon
     */
    protected $purchaseLoadingWagon;

    /**
     * @var Form\PurchaseUnloadingWagon
     */
    protected $purchaseUnloadingWagonForm;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * @var Form\PurchaseImportWagon
     */
    protected $importWagonForm;

    /**
     * PurchaseWagonController constructor.
     * @param ContractorCompanyManager        $companyManager
     * @param Service\PurchaseContractManager $purchaseContractManager
     * @param Service\PurchaseWagonManager    $purchaseWagonManager
     * @param Form\PurchaseEditWagon          $purchaseEditWagonForm
     * @param Form\PurchaseLoadingWagon       $purchaseLoadingWagon
     * @param Form\PurchaseUnloadingWagon     $purchaseUnloadingWagonForm
     * @param WarehouseLogManager             $warehouseLogManager
     * @param Form\PurchaseImportWagon        $importWagonForm
     */
    public function __construct(ContractorCompanyManager $companyManager,
                                Service\PurchaseContractManager $purchaseContractManager,
                                Service\PurchaseWagonManager $purchaseWagonManager,
                                Form\PurchaseEditWagon $purchaseEditWagonForm,
                                Form\PurchaseLoadingWagon $purchaseLoadingWagon,
                                Form\PurchaseUnloadingWagon $purchaseUnloadingWagonForm,
                                WarehouseLogManager $warehouseLogManager,
                                Form\PurchaseImportWagon $importWagonForm) {
        $this->companyManager = $companyManager;
        $this->purchaseContractManager = $purchaseContractManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->purchaseEditWagonForm = $purchaseEditWagonForm;
        $this->purchaseLoadingWagon = $purchaseLoadingWagon;
        $this->purchaseUnloadingWagonForm = $purchaseUnloadingWagonForm;
        $this->warehouseLogManager = $warehouseLogManager;
        $this->importWagonForm = $importWagonForm;
    }

    /**
     * @return Http\Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->purchaseContractManager->getContractById($contractId);

        $filterData = $this->params()->fromQuery();

        $name = sprintf('%s-purchase-wagons-export.xlsx', $contract->getContractNumber());

        $filename = $this->purchaseWagonManager->getWagonsExportData($contractId, $filterData);

        $response = new Http\Response\Stream();
        $response->setStream(fopen($filename, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName(basename($filename));
        $headers = new Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . basename($name) . '"',
            'Content-Type'        => 'application/octet-stream',
            'Content-Length'      => filesize($filename),
            'Expires'             => '@0',
            'Cache-Control'       => 'must-revalidate',
            'Pragma'              => 'public',
        ));
        $response->setHeaders($headers);
        return $response;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function importAction() {
        $companyId = $this->params()->fromRoute('company');
        $contractId = $this->params()->fromRoute('contract');

        $form = $this->importWagonForm;

        $company = $this->companyManager->getContractorById($companyId);
        $contract = $this->purchaseContractManager->getContractById($contractId);


        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $tmpFile = $this->params()->fromFiles('file');
                $result = $this->purchaseWagonManager->putWagonsImportData($contract, $form->getData(), $tmpFile);
                $messenger = $this->plugin('FlashMessenger');

                if ($result::STATUS_WARNING == $result->getStatus()) {
                    foreach ($result->getSource() as $message) {
                        $messenger->addMessage($message, $result->getStatus());
                    }
                } else {
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                }

                return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', ['company' => $companyId, 'id' => $contractId]);
            }
        } elseif ($contract) {
            $form->get('contract_id')->setValue($contract->getContractId());
        }

        $view = new ViewModel();
        $view->setVariable('form', $form);
        $view->setVariable('company', $company);
        $view->setVariable('contract', $contract);
        return $view;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function editAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $form = $this->purchaseEditWagonForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->purchaseWagonManager->saveWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', [
                    'company' => $company->getContractorId(),
                    'id'      => $contractId]);
            }
        } elseif ($purchaseWagonId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseWagonManager->getWagonById($purchaseWagonId);
            $form->bind($object);
        }

        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function loadingAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->purchaseContractManager->getContractById($contractId);
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $form = $this->purchaseLoadingWagon;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getData();
                $object->setContractId($contract->getContractId());
                $result = $this->purchaseWagonManager->loadWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', [
                    'company' => $company->getContractorId(),
                    'id'      => $contractId]);
            }
        } elseif ($purchaseWagonId = $this->params()->fromRoute('id')) {
            $object = $this->purchaseWagonManager->getWagonById($purchaseWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function unloadingAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
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
                return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', [
                    'company' => $company->getContractorId(),
                    'id'      => $contractId]);
            }
        } elseif ($ids = $this->params()->fromQuery('ids')) {
            $date = $this->params()->fromQuery('date', date('d.m.Y'));
            $result = $this->purchaseWagonManager->unloadWagonMultiple($ids, $contract, $date);
            $messenger->addMessage($result->getMessage(), $result->getStatus());
            return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', [
                'company' => $company->getContractorId(),
                'id'      => $contractId]);
        } else {
            $form->populateValues([
                'wagon_id'         => $wagon->getWagonId(),
                'contract_id'      => $wagon->getContractId(),
                'unloading_weight' => $wagon->getLoadingWeight(),
            ]);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $companyId = $this->params()->fromRoute('company');
        $contractId = $this->params()->fromRoute('contract');
        if ($ids = $this->params()->fromQuery('ids')) {
            $result = $this->purchaseWagonManager->deleteWagonMultiple($ids);
        } else {
            $purchaseWagonId = $this->params()->fromRoute('id');
            $result = $this->purchaseWagonManager->deleteWagonById($purchaseWagonId);
        }
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('dashboard/purchaseContract/advanced', ['company' => $companyId, 'id' => $contractId]);
    }


}