<?php

namespace Bank\Controller;

use Bank\Entity\RecordEntity;
use Bank\Exception\DeleteErrorException;
use Bank\Exception\NotFoundException;
use Bank\Exception\SaveErrorException;
use Bank\Form\FilterForm;
use Bank\Form\ImportForm;
use Bank\Form\RecordForm;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorCompanyManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecordController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RecordForm
     */
    protected $recordForm;

    /**
     * @var FilterForm
     */
    protected $filterForm;

    /**
     * @var ImportForm
     */
    protected $importForm;

    /**
     * @var RecordManager
     */
    protected $recordManager;

    /**
     * @var ContractorCompanyManager
     */
    protected $companyManager;

    /**
     * RecordController constructor.
     * @param RecordForm $recordForm
     * @param FilterForm $filterForm
     * @param ImportForm $importForm
     * @param RecordManager $recordManager
     * @param ContractorCompanyManager $companyManager
     */
    public function __construct(RecordForm $recordForm,
                                FilterForm $filterForm,
                                ImportForm $importForm,
                                RecordManager $recordManager,
                                ContractorCompanyManager $companyManager) {
        $this->recordForm = $recordForm;
        $this->filterForm = $filterForm;
        $this->importForm = $importForm;
        $this->recordManager = $recordManager;
        $this->companyManager = $companyManager;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function importAction() {
        $companyId = $this->params()->fromRoute('company');
        $form = $this->importForm;
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $tmpFile = $this->params()->fromFiles('file');
                $result = $this->recordManager->putImportData($companyId, $tmpFile);
                $messenger = $this->plugin('FlashMessenger');

                if ($result::STATUS_WARNING == $result->getStatus()) {
                    foreach ($result->getSource() as $message) {
                        $messenger->addMessage($message, $result->getStatus());
                    }
                } else {
                    $messenger->addMessage($result->getMessage(), $result->getStatus());
                }

                return $this->plugin('Redirect')->toRoute('dashboard/bank', ['company' => $companyId, 'id' => $contractId]);
            }
        }
    }

    /**
     * @return ViewModel
     * @throws NotFoundException
     * @throws \Contractor\Exception\ErrorException
     */
    public function indexAction() {
        $queryParams = $this->params()->fromQuery();
        $filterForm = $this->filterForm;
        $filterForm->setData($queryParams);
        $importForm = $this->importForm;

        $companyId = $this->params()->fromRoute('company');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->recordManager->fetchAllWithPaginator($companyId);
        $paginator->setCurrentPageNumber($pageNumber);
        $company = $this->companyManager->getContractorById($companyId);

        if (count($queryParams) && $filterForm->isValid()) {
            $filterData = $filterForm->getData();
            $data = $this->recordManager->getChessData($companyId, $filterData['period_begin'], $filterData['period_end']);
        } else {
            $data = $this->recordManager->getChessData($companyId);
        }


        $view = new ViewModel();
        $view->setVariable('data', $data);
        $view->setVariable('companyId', $companyId);
        $view->setVariable('company', $company);
        $view->setVariable('paginator', $paginator);
        $view->setVariable('filterForm', $filterForm);
        $view->setVariable('importForm', $importForm);
        $view->setVariable('messenger', $this->plugin('flashMessenger'));
        return $view;
    }

    /**
     * @return ViewModel
     * @throws \Contractor\Exception\ErrorException
     */
    public function editAction() {
        $companyId = $this->params()->fromRoute('company');
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
                $this->redirect()->toRoute('dashboard/bank', ['company' => $recordEntity->getCompanyId()]);
            }
        } elseif ($recordId = $this->params()->fromRoute('id')) {
            try {
                $recordEntity = $this->recordManager->fetchOneById($recordId);
                $form->bind($recordEntity);
            } catch (NotFoundException $notFoundException) {
                $this->plugin('FlashMessenger')->addErrorMessage($notFoundException->getMessage());
                $this->redirect()->toRoute('dashboard/bank', ['company' => $companyId]);
            }
        } else {
            $form->get('company_id')->setValue($companyId);
            $form->get('bank_id')->setValue($this->params()->fromQuery('bank'));
            $form->get('date')->setValue($this->params()->fromQuery('date'));
        }

        $company = $this->companyManager->getContractorById($companyId);

        $view = new ViewModel();
        $view->setVariable('companyId', $companyId);
        $view->setVariable('company', $company);
        $view->setVariable('messenger', $this->plugin('flashMessenger'));
        $view->setVariable('form', $form);
        return $view;
    }

    public function deleteAction() {
        $companyId = $this->params()->fromRoute('company');
        $recordId = $this->params()->fromRoute('id');
        try {
            $this->recordManager->deleteRecord($recordId);
            $this->plugin('FlashMessenger')->addSuccessMessage('Record data has been successfully deleted');
        } catch (DeleteErrorException $deleteErrorException) {
            $this->plugin('FlashMessenger')->addErrorMessage($deleteErrorException->getMessage());
        }
        $this->redirect()->toRoute('dashboard/bank', ['company' => $companyId]);
    }

}