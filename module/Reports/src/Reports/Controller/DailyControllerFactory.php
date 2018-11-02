<?php

namespace Reports\Controller;

use Application\Model\AccountsPayableService;
use Application\Model\AccountsReceivableService;
use Application\Model\CheckingAccountService;
use Application\Model\ProvidersReceivableService;
use Application\Service\Repository\ApiDb;
use Bank\Service\RecordManager;
use Contractor\Service\ContractorCompanyManager;
use Interop\Container\ContainerInterface;
use Reports\Form\DailyFilterForm;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class DailyControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $accountsPayableService = $container->get(AccountsPayableService::class);
        $accountsReceivableService = $container->get(AccountsReceivableService::class);
        $checkingAccountService = $container->get(CheckingAccountService::class);
        $dailyFilterForm = $container->get('FormElementManager')->get(DailyFilterForm::class);
        $db = $container->get(Adapter::class);
        $contractorCompanyManager = $container->get(ContractorCompanyManager::class);
        $apiDbRepository = $container->get(ApiDb::class);
        $recordManager = $container->get(RecordManager::class);
        $providersReceivableService = $container->get(ProvidersReceivableService::class);
        return new DailyController(
            $accountsPayableService,
            $accountsReceivableService,
            $checkingAccountService,
            $dailyFilterForm, $db, $contractorCompanyManager, $apiDbRepository, $recordManager,
            $providersReceivableService);
    }

}