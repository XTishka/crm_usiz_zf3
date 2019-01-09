<?php

namespace Document\Service;

use Application\Service\TaxManager;
use Interop\Container\ContainerInterface;
use Transport\Service\RateManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseRecountShippingCostFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $rateManager = $container->get(RateManager::class);
        /** @var PurchaseWagonManager $purchaseWagonManager */
        $purchaseWagonManager = $container->get(PurchaseWagonManager::class);
        $taxManager = $container->get(TaxManager::class);
        return new PurchaseRecountShippingCost($rateManager, $purchaseWagonManager, $taxManager);
    }

}