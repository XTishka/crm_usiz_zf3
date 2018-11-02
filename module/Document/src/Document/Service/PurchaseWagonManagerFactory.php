<?php

namespace Document\Service;

use Contractor\Service\ContractorCompanyManager;
use Document\InputFilter;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Transport\Service\CarrierManager;
use Transport\Service\RateManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseWagonManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $purchaseContractDbRepository = $container->get(Repository\PurchaseContractDb::class);
        $purchaseWagonDbRepository = $container->get(Repository\PurchaseWagonDb::class);
        $purchaseWagonInputFilter = $container->get('InputFilterManager')->get(InputFilter\PurchaseWagon::class);
        $financeManager = $container->get(FinanceManager::class);
        $rateManager = $container->get(RateManager::class);
        $carrierManager = $container->get(CarrierManager::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        $contractorCompanyManager = $container->get(ContractorCompanyManager::class);
        return new PurchaseWagonManager($purchaseContractDbRepository, $purchaseWagonDbRepository, $purchaseWagonInputFilter,
            $financeManager, $rateManager, $carrierManager, $warehouseLogManager, $contractorCompanyManager);
    }

}