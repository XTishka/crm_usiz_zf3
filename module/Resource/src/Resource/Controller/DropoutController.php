<?php

namespace Resource\Controller;

use Resource\Exception\DeleteErrorException;
use Resource\Exception\NotFoundException;
use Resource\Exception\SaveErrorException;
use Resource\Form\DropoutForm;
use Resource\Service\DropoutManager;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DropoutController extends AbstractActionController {
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DropoutForm
     */
    protected $dropoutForm;

    /**
     * @var DropoutManager
     */
    protected $dropoutManager;

    /**
     * DropoutController constructor.
     * @param DropoutForm    $dropoutForm
     * @param DropoutManager $dropoutManager
     */
    public function __construct(DropoutForm $dropoutForm, DropoutManager $dropoutManager) {
        $this->dropoutForm = $dropoutForm;
        $this->dropoutManager = $dropoutManager;
    }

    /**
     * @return JsonModel
     */
    public function ajaxValueAction() {
        $providerId = $this->params()->fromRoute('provider');
        $date = $this->params()->fromQuery('date');
        $materialId = $this->params()->fromQuery('material');
        $json = new JsonModel();
        try {
            $dropoutEntity = $this->dropoutManager->fetchOneByProviderId($providerId, $materialId, $date);
            $json->setVariable('provider', $dropoutEntity->getProviderId());
            $json->setVariable('value', $dropoutEntity->getValue());
            $json->setVariable('status', 'success');
        } catch (NotFoundException $exception) {
            $json->setVariable('status', 'error');
            $json->setVariable('message', $exception->getMessage());
        }
        return $json;
    }

    /**
     * @return ViewModel
     * @throws NotFoundException
     */
    public function indexAction() {
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->dropoutManager->fetchAllWithPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $view = new ViewModel();
        $view->setVariable('messenger', $this->plugin('FlashMessenger'));
        $view->setVariable('paginator', $paginator);
        return $view;
    }

    /**
     * @return ViewModel
     */
    public function editAction() {
        $form = $this->dropoutForm;
        if ($this->request->isPost()) {
            $dataFromPost = $this->params()->fromPost();
            $form->setData($dataFromPost);
            if ($form->isValid()) {
                $dropoutEntity = $form->getObject();
                try {
                    $this->dropoutManager->saveDropout($dropoutEntity);
                    $this->plugin('FlashMessenger')->addSuccessMessage('The dropout data was successfully saved');
                } catch (SaveErrorException $saveErrorException) {
                    $this->plugin('FlashMessenger')->addErrorMessage($saveErrorException->getMessage());
                }
                $this->redirect()->toRoute('dropout');
            }
        } elseif ($dropoutId = $this->params()->fromRoute('id')) {
            try {
                $dropoutEntity = $this->dropoutManager->fetchOneById($dropoutId);
                $form->bind($dropoutEntity);
            } catch (NotFoundException $notFoundException) {
                $this->plugin('FlashMessenger')->addErrorMessage($notFoundException->getMessage());
                $this->redirect()->toRoute('dropout');
            }
        }
        $view = new ViewModel();
        $view->setVariable('form', $form);
        return $view;
    }

    public function deleteAction() {
        $dropoutId = $this->params()->fromRoute('id');
        try {
            $this->dropoutManager->deleteDropout($dropoutId);
            $this->plugin('FlashMessenger')->addSuccessMessage('Dropout data has been successfully deleted');
        } catch (DeleteErrorException $deleteErrorException) {
            $this->plugin('FlashMessenger')->addErrorMessage($deleteErrorException->getMessage());
        }
        $this->redirect()->toRoute('dropout');
    }
}