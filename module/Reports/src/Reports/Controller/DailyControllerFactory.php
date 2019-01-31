<?php

namespace Reports\Controller;

use Application\Model\CheckingAccountService;
use Application\Model\Finance\AccountPayableService;
use Application\Model\Finance\PrepayFromCustomerService;
use Application\Model\Finance\PrepayToCarrierService;
use Application\Model\Finance\PrepayToProviderService;
use Application\Model\Finance\TotalReceivableService;
use Application\Service\Repository\ApiDb;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorCompanyManager;
use Interop\Container\ContainerInterface;
use Reports\Form\DailyFilterForm;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class DailyControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $db = $container->get(Adapter::class);
        $accountPayableService = $container->get(AccountPayableService::class);
        $totalReceivableService = $container->get(TotalReceivableService::class);
        $checkingAccountService = $container->get(CheckingAccountService::class);
        $prepayFromCustomerService = $container->get(PrepayFromCustomerService::class);
        $dailyFilterForm = $container->get('FormElementManager')->get(DailyFilterForm::class);
        $contractorCompanyManager = $container->get(ContractorCompanyManager::class);
        $apiDbRepository = $container->get(ApiDb::class);
        $recordManager = $container->get(RecordManager::class);
        $prepayToProviderService = $container->get(PrepayToProviderService::class);
        $prepayToCarrierService = $container->get(PrepayToCarrierService::class);
        return new DailyController(
            $db,
            $accountPayableService,
            $totalReceivableService,
            $checkingAccountService,
            $prepayFromCustomerService,
            $dailyFilterForm,
            $contractorCompanyManager,
            $apiDbRepository,
            $recordManager,
            $prepayToProviderService,
            $prepayToCarrierService
        );
    }

}