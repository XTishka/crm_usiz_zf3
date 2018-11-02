<?php

namespace Resource\Controller;

use Resource\Form;
use Resource\Service\MaterialManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MaterialController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var MaterialManager
     */
    protected $materialManager;

    /**
     * @var Form\Material
     */
    protected $materialForm;

    /**
     * MaterialController constructor.
     * @param MaterialManager $materialManager
     * @param Form\Material $materialForm
     */
    public function __construct(MaterialManager $materialManager, Form\Material $materialForm) {
        $this->materialManager = $materialManager;
        $this->materialForm = $materialForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->materialManager->getMaterialsPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->materialForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->materialManager->saveMaterial($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('material/edit', ['id' => $result->getSource()->getMaterialId()]);
                return $this->plugin('Redirect')->toRoute('material');
            }
        } elseif ($materialId = $this->params()->fromRoute('id')) {
            $object = $this->materialManager->getMaterialById($materialId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $materialId = $this->params()->fromRoute('id');
        $result = $this->materialManager->deleteMaterialById($materialId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('material');
    }

}