<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Controller;

use Application\Model\Finance\AccountPayableService;
use Application\Model\Finance\CharterCapitalContainer;
use Application\Model\Finance\CharterCapitalService;
use Application\Model\Finance\CustomerReceivableService;
use Application\Model\Finance\DebtToCarrierService;
use Application\Model\Finance\DebtToOtherService;
use Application\Model\Finance\DebtToPlantService;
use Application\Model\Finance\DebtToProviderService;
use Application\Model\Finance\PrepayFromCustomerService;
use Application\Model\Finance\PrepayToCarrierService;
use Application\Model\Finance\PrepayToOtherService;
use Application\Model\Finance\PrepayToPlantService;
use Application\Model\Finance\PrepayToProviderService;
use Application\Model\Finance\TotalReceivableService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class TestController extends AbstractActionController {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TestController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }


    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction() {

        $cid = 43;

        $view = new ViewModel();

        /** @var AccountPayableService $accountsPayableService */
        $accountPayableService = $this->container->get(AccountPayableService::class);
        $accountPayableContainer = $accountPayableService->getRecords($cid);
        $view->setVariable('accountPayableContainer', $accountPayableContainer);

        /** @var TotalReceivableService $totalReceivableService */
        $totalReceivableService = $this->container->get(TotalReceivableService::class);
        $totalReceivableContainer = $totalReceivableService->getRecords($cid);
        $view->setVariable('totalReceivableContainer', $totalReceivableContainer);

        /** @var DebtToProviderService $debtToProviderService */
        $debtToProviderService = $this->container->get(DebtToProviderService::class);
        $debtToProviderContainer = $debtToProviderService->getRecords($cid);
        $view->setVariable('debtToProviderContainer', $debtToProviderContainer);

        /** @var CharterCapitalService $charterCapitalService */
        $charterCapitalService = $this->container->get(CharterCapitalService::class);
        $charterCapitalContainer = $charterCapitalService->getRecords($cid);
        $view->setVariable('charterCapitalContainer', $charterCapitalContainer);

        /** @var PrepayFromCustomerService $prepayFromCustomerService */
        $prepayFromCustomerService = $this->container->get(PrepayFromCustomerService::class);
        $prepayFromCustomerContainer = $prepayFromCustomerService->getRecords($cid);
        $view->setVariable('prepayFromCustomerContainer', $prepayFromCustomerContainer);

        /** @var CustomerReceivableService $customerReceivableService */
        $customerReceivableService = $this->container->get(CustomerReceivableService::class);
        $customerReceivableContainer = $customerReceivableService->getRecords($cid);
        $view->setVariable('customerReceivableContainer', $customerReceivableContainer);

        /** @var PrepayToProviderService $prepayToProviderService */
        $prepayToProviderService = $this->container->get(PrepayToProviderService::class);
        $prepayToProviderContainer = $prepayToProviderService->getRecords($cid);
        $view->setVariable('prepayToProviderContainer', $prepayToProviderContainer);

        /** @var DebtToCarrierService $debtToCarrierService */
        $debtToCarrierService = $this->container->get(DebtToCarrierService::class);
        $debtToCarrierContainer = $debtToCarrierService->getRecords($cid);
        $view->setVariable('debtToCarrierContainer', $debtToCarrierContainer);

        /** @var PrepayToCarrierService $prepayToCarrierService */
        $prepayToCarrierService = $this->container->get(PrepayToCarrierService::class);
        $prepayToCarrierContainer = $prepayToCarrierService->getRecords($cid);
        $view->setVariable('prepayToCarrierContainer', $prepayToCarrierContainer);

        /** @var DebtToPlantService $debtToPlantService */
        $debtToPlantService = $this->container->get(DebtToPlantService::class);
        $debtToPlantContainer = $debtToPlantService->getRecords($cid);
        $view->setVariable('debtToPlantContainer', $debtToPlantContainer);

        /** @var PrepayToPlantService $prepayToPlantService */
        $prepayToPlantService = $this->container->get(PrepayToPlantService::class);
        $prepayToPlantContainer = $prepayToPlantService->getRecords($cid);
        $view->setVariable('prepayToPlantContainer', $prepayToPlantContainer);

        /** @var DebtToOtherService $debtToOtherService */
        $debtToOtherService = $this->container->get(DebtToOtherService::class);
        $debtToOtherContainer = $debtToOtherService->getRecords($cid);
        $view->setVariable('debtToOtherContainer', $debtToOtherContainer);

        /** @var PrepayToOtherService $prepayToOtherService */
        $prepayToOtherService = $this->container->get(PrepayToOtherService::class);
        $prepayToOtherContainer = $prepayToOtherService->getRecords($cid);
        $view->setVariable('prepayToOtherContainer', $prepayToOtherContainer);

        return $view;
    }

}