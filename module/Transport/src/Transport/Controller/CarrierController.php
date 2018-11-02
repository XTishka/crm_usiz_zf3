<?php

namespace Transport\Controller;

use Transport\Form;
use Transport\Service\CarrierManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CarrierController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var CarrierManager
     */
    protected $carrierManager;

    /**
     * @var Form\Carrier
     */
    protected $carrierForm;

    /**
     * CarrierController constructor.
     * @param CarrierManager $carrierManager
     * @param Form\Carrier $carrierForm
     */
    public function __construct(CarrierManager $carrierManager, Form\Carrier $carrierForm) {
        $this->carrierManager = $carrierManager;
        $this->carrierForm = $carrierForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->carrierManager->getCarriersPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->carrierForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->carrierManager->saveCarrier($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('carrier/edit', ['id' => $result->getSource()->getCarrierId()]);
                return $this->plugin('Redirect')->toRoute('carrier');
            }
        } elseif ($carrierId = $this->params()->fromRoute('id')) {
            $object = $this->carrierManager->getCarrierById($carrierId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $carrierId = $this->params()->fromRoute('id');
        $result = $this->carrierManager->deleteCarrierById($carrierId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('carrier');
    }

    public function valueOptionsAction() {
        $params = $this->params()->fromPost();
        $options = $this->carrierManager->getCarrierValueOptionsByParams($params);
        return new JsonModel($options);
    }

}