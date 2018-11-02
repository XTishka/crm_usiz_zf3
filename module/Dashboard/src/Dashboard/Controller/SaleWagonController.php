<?php

namespace Dashboard\Controller;

use Contractor\Service\ContractorCompanyManager;
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
     * @var ContractorCompanyManager
     */
    protected $companyManager;

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
     * @var Form\SaleImportWagon
     */
    protected $importWagonForm;

    /**
     * SaleWagonController constructor.
     * @param ContractorCompanyManager    $companyManager
     * @param Service\SaleContractManager $saleContractManager
     * @param Service\SaleWagonManager    $saleWagonManager
     * @param Form\SaleEditWagon          $saleEditWagonForm
     * @param Form\SaleLoadingWagon       $saleLoadingWagon
     * @param WarehouseLogManager         $warehouseLogManager
     * @param Form\SaleImportWagon        $importWagonForm
     */
    public function __construct(ContractorCompanyManager $companyManager,
                                Service\SaleContractManager $saleContractManager,
                                Service\SaleWagonManager $saleWagonManager,
                                Form\SaleEditWagon $saleEditWagonForm,
                                Form\SaleLoadingWagon $saleLoadingWagon,
                                WarehouseLogManager $warehouseLogManager,
                                Form\SaleImportWagon $importWagonForm) {
        $this->companyManager = $companyManager;
        $this->saleContractManager = $saleContractManager;
        $this->saleWagonManager = $saleWagonManager;
        $this->saleEditWagonForm = $saleEditWagonForm;
        $this->saleLoadingWagon = $saleLoadingWagon;
        $this->warehouseLogManager = $warehouseLogManager;
        $this->importWagonForm = $importWagonForm;
    }

    /**
     * @return Http\Response\Stream
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportAction() {
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->saleContractManager->getContractById($contractId);

        $filterData = $this->params()->fromQuery();

        $name = sprintf('%s-sale-wagons-export.xlsx', $contract->getContractNumber());

        $filename = $this->saleWagonManager->getWagonsExportData($contractId, $filterData);

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
        $contract = $this->saleContractManager->getContractById($contractId);


        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $tmpFile = $this->params()->fromFiles('file');
                $result = $this->saleWagonManager->putWagonsImportData($contract, $form->getData(), $tmpFile);
                $messenger = $this->plugin('FlashMessenger');

                if ($result::STATUS_WARNING == $result->getStatus()) {
                    foreach ($result->getSource() as $message) {
                        $messenger->addMessage($message, $result->getStatus());
                    }
                } else {
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                }

                return $this->plugin('Redirect')->toRoute('dashboard/saleContract/advanced', ['company' => $companyId, 'id' => $contractId]);
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
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
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
                return $this->plugin('Redirect')->toRoute('dashboard/saleContract/advanced', ['id' => $contractId, 'company' => $company->getContractorId()]);
            }
        } else if ($saleWagonId = $this->params()->fromRoute('id')) {
            $object = $this->saleWagonManager->getWagonById($saleWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function loadingAction() {
        $companyId = $this->params()->fromRoute('company');
        $company = $this->companyManager->getContractorById($companyId);
        $contractId = $this->params()->fromRoute('contract');
        $contract = $this->saleContractManager->getContractById($contractId);
        $form = $this->saleLoadingWagon;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($contract::CONDITIONS_TYPE_FCA == $contract->getConditions()) {
                $form->getInputFilter()->get('carrier_id')->setRequired(false);
                $form->getInputFilter()->get('rate_id')->setRequired(false);
            }

            if ($form->isValid()) {
                $object = $form->getObject();
                $object->setContractId($contract->getContractId());
                $result = $this->saleWagonManager->loadWagon($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('dashboard/saleContract/advanced', ['id' => $contractId, 'company' => $companyId]);
            }
        } else if ($saleWagonId = $this->params()->fromRoute('id')) {
            $object = $this->saleWagonManager->getWagonById($saleWagonId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('company', $company);
        $viewModel->setVariable('contract', $contract);
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $companyId = $this->params()->fromRoute('company');
        $contractId = $this->params()->fromRoute('contract');

        if ($ids = $this->params()->fromQuery('ids')) {
            $result = $this->saleWagonManager->deleteWagonMultiple($ids);
        } else {
            $saleWagonId = $this->params()->fromRoute('id');
            $result = $this->saleWagonManager->deleteWagonById($saleWagonId);
        }
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        return $this->plugin('Redirect')->toRoute('dashboard/saleContract/advanced', [
            'company' => $companyId,
            'id'      => $contractId]);
    }


}