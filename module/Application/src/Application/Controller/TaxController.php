<?php

namespace Application\Controller;

use Application\Form;
use Application\Service\TaxManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TaxController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var TaxManager
     */
    protected $taxManager;

    /**
     * @var Form\Tax
     */
    protected $taxForm;

    /**
     * TaxController constructor.
     * @param TaxManager $taxManager
     * @param Form\Tax $taxForm
     */
    public function __construct(TaxManager $taxManager, Form\Tax $taxForm) {
        $this->taxManager = $taxManager;
        $this->taxForm = $taxForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->taxManager->getTaxesPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->taxForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->taxManager->saveTax($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('tax/edit', ['id' => $result->getSource()->getTaxId()]);
                return $this->plugin('Redirect')->toRoute('tax');
            }
        } elseif ($taxId = $this->params()->fromRoute('id')) {
            $object = $this->taxManager->getTaxById($taxId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $taxId = $this->params()->fromRoute('id');
        $result = $this->taxManager->deleteTaxById($taxId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('tax');
    }

}