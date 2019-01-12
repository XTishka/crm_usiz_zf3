<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 */

namespace Application\Controller\Api;

use Application\Model\Finance\DebtToCarrierService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DebtToCarrierController extends AbstractActionController {

    /**
     * @var DebtToCarrierService
     */
    protected $service;

    /**
     * DebtToCarrierController constructor.
     * @param DebtToCarrierService $service
     */
    public function __construct(DebtToCarrierService $service) {
        $this->service = $service;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction() {
        $companyId = $this->params()->fromRoute('company');
        if ($date = $this->params()->fromQuery('date')) {
            $this->service->setDate(\DateTime::createFromFormat('d.m.Y', $date));
        }
        $container = $this->service->getRecords($companyId);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('container', $container);
        return $viewModel;
    }

}