<?php

namespace Application\Controller;

use Application\Form;
use Application\Service\CountryManager;
use Zend\Http;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CountryController extends AbstractActionController {

    /**
     * @var Http\Request
     */
    protected $request;

    /**
     * @var CountryManager
     */
    protected $countryManager;

    /**
     * @var Form\Country
     */
    protected $countryForm;

    /**
     * CountryController constructor.
     * @param CountryManager $countryManager
     * @param Form\Country $countryForm
     */
    public function __construct(CountryManager $countryManager, Form\Country $countryForm) {
        $this->countryManager = $countryManager;
        $this->countryForm = $countryForm;
    }

    public function indexAction() {
        $messenger = $this->plugin('FlashMessenger');
        $pageNumber = $this->params()->fromQuery('page');
        $paginator = $this->countryManager->getCountriesPaginator();
        $paginator->setCurrentPageNumber($pageNumber);
        $viewModel = new ViewModel();
        $viewModel->setVariable('messenger', $messenger);
        $viewModel->setVariable('paginator', $paginator);
        return $viewModel;
    }

    public function editAction() {
        $form = $this->countryForm;
        $messenger = $this->plugin('FlashMessenger');
        if ($this->request->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $object = $form->getObject();
                $result = $this->countryManager->saveCountry($object);
                $messenger->addMessage($result->getMessage(), $result->getStatus());
                if ($this->params()->fromPost('save_and_remain'))
                    return $this->plugin('Redirect')->toRoute('country/edit', ['id' => $result->getSource()->getCountryId()]);
                return $this->plugin('Redirect')->toRoute('country');
            }
        } elseif ($countryId = $this->params()->fromRoute('id')) {
            $object = $this->countryManager->getCountryById($countryId);
            $form->bind($object);
        }
        $viewModel = new ViewModel();
        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $messenger);
        return $viewModel;
    }

    public function deleteAction() {
        $countryId = $this->params()->fromRoute('id');
        $result = $this->countryManager->deleteCountryById($countryId);
        $this->plugin('FlashMessenger')->addMessage($result->getMessage(), $result->getStatus());
        $this->plugin('Redirect')->toRoute('country');
    }

}