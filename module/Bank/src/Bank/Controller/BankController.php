<?php

namespace Bank\Controller;

use Bank\Exception\DeleteErrorException;
use Bank\Exception\NotFoundException;
use Bank\Exception\SaveErrorException;
use Bank\Form\BankForm;
use Bank\Service\BankManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BankController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var BankForm
     */
    protected $bankForm;

    /**
     * @var BankManager
     */
    protected $bankManager;

    /**
     * BankController constructor.
     * @param BankForm    $bankForm
     * @param BankManager $bankManager
     */
    public function __construct(BankForm $bankForm, BankManager $bankManager) {
        $this->bankForm = $bankForm;
        $this->bankManager = $bankManager;
    }

    /**
     * @return ViewModel
     * @throws NotFoundException
     */
    public function indexAction() {
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->bankManager->fetchAllWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $view = new ViewModel();
        $view->setVariable('messenger', $this->plugin('FlashMessenger'));
        $view->setVariable('paginator', $paginator);
        return $view;
    }

    public function editAction() {
        $form = $this->bankForm;
        if ($this->request->isPost()) {
            $dataFromPost = $this->params()->fromPost();
            $form->setData($dataFromPost);
            if ($form->isValid()) {
                $bankEntity = $form->getObject();
                try {
                    $this->bankManager->saveBank($bankEntity);
                    $this->plugin('FlashMessenger')->addSuccessMessage('The bank data was successfully saved');
                } catch (SaveErrorException $saveErrorException) {
                    $this->plugin('FlashMessenger')->addErrorMessage($saveErrorException->getMessage());
                }
                $this->redirect()->toRoute('bank');
            }
        } elseif ($bankId = $this->params()->fromRoute('id')) {
            try {
                $bankEntity = $this->bankManager->fetchOneById($bankId);
                $form->bind($bankEntity);
            } catch (NotFoundException $notFoundException) {
                $this->plugin('FlashMessenger')->addErrorMessage($notFoundException->getMessage());
                $this->redirect()->toRoute('bank');
            }
        }
        $view = new ViewModel();
        $view->setVariable('form', $form);
        return $view;
    }

    public function deleteAction() {
        $bankId = $this->params()->fromRoute('id');
        try {
            $this->bankManager->deleteBank($bankId);
            $this->plugin('FlashMessenger')->addSuccessMessage('Bank data has been successfully deleted');
        } catch (DeleteErrorException $deleteErrorException) {
            $this->plugin('FlashMessenger')->addErrorMessage($deleteErrorException->getMessage());
        }
        $this->redirect()->toRoute('bank');
    }

}