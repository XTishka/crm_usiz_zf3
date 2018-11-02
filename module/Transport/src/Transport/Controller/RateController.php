<?php

namespace Transport\Controller;

use Transport\Domain\RateEntity;
use Transport\Form;
use Transport\Service\RateManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RateController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var RateManager
     */
    protected $rateManager;

    /**
     * @var Form\Rate
     */
    protected $rateForm;

    /**
     * @var Form\RateFilter
     */
    protected $rateFilterForm;

    protected $addRateForm;

    /**
     * RateController constructor.
     * @param RateManager     $rateManager
     * @param Form\AddRate    $addRateForm
     * @param Form\Rate       $rateForm
     * @param Form\RateFilter $rateFilterForm
     */
    public function __construct(RateManager $rateManager, Form\AddRate $addRateForm, Form\Rate $rateForm, Form\RateFilter $rateFilterForm) {
        $this->rateManager = $rateManager;
        $this->addRateForm = $addRateForm;
        $this->rateForm = $rateForm;
        $this->rateFilterForm = $rateFilterForm;
    }

    public function indexAction() {
        $queryParams = $this->params()->fromQuery();
        $filterForm = $this->rateFilterForm;
        if (count($queryParams)) {
            $filterForm->populateValues($queryParams);
        } else {
            $filterForm->populateValues($this->rateManager->getFilterParams());
        }
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->rateManager->getRatesPaginator(null, $queryParams);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage(20);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        $viewModel->setVariable('filterForm', $filterForm);
        return $viewModel;
    }

    public function clearFilterAction() {
        $this->rateManager->resetFilterParams();
        $this->redirect()->toRoute('rate');
    }

    public function addAction() {
        $form = $this->addRateForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                /**
                 * @var RateEntity   $object
                 * @var \ArrayObject $one
                 */
                $object = $form->getObject();

                foreach ($object->getMultipleWeight() as $one) {
                    $current = clone $object;
                    $current->setMinWeight($one->offsetGet('min_weight'));
                    $current->setWeight($one->offsetGet('weight'));
                    $current->setPrice($one->offsetGet('price'));
                    $result = $this->rateManager->saveRate($current);
                }

                $messenger->addMessage($result->getMessage(), $result->getStatus());
                return $this->plugin('Redirect')->toRoute('rate');
            }
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->rateForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->rateManager->saveRate($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('rate/edit', ['id' => $result->getSource()->getRateId()]);
                return $this->plugin('Redirect')->toRoute('rate');
            }
        } else if ($rateId = $this->params()->fromRoute('id')) {
            $object = $this->rateManager->getRateById($rateId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $rateId = $this->params()->fromRoute('id');
        $result = $this->rateManager->deleteRateById($rateId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('rate');
    }

    public function ajaxRatesAction() {
        $params = $this->params()->fromPost();
        $options = $this->rateManager->getRatesByParams($params);
        return new JsonModel($options);
    }

    public function ajaxValuesAction() {
        $rateId = $this->params()->fromQuery('rate');
        $options = $this->rateManager->getValuesByRateId($rateId);
        return new JsonModel($options);
    }

}