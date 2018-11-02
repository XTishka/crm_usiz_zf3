<?php

namespace Document\Service;

use Contractor\Service\ContractorCompanyManager;
use Contractor\Service\ContractorPlantManager;
use Document\InputFilter;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Transport\Service\RateManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleWagonManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $saleContractDbRepository = $container->get(Repository\SaleContractDb::class);
        $companyManager = $container->get(ContractorCompanyManager::class);
        $plantManager = $container->get(ContractorPlantManager::class);
        $saleWagonInputFilter = $container->get('InputFilterManager')->get(InputFilter\SaleWagon::class);
        $saleWagonDbRepository = $container->get(Repository\SaleWagonDb::class);
        $financeLogManager = $container->get(FinanceManager::class);
        $rateManager = $container->get(RateManager::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        return new SaleWagonManager($saleContractDbRepository, $companyManager, $plantManager, $saleWagonInputFilter, $saleWagonDbRepository, $financeLogManager, $rateManager, $warehouseLogManager);
    }

}