<?php

namespace Document\Service;

use Application\Service\TaxManager;
use Interop\Container\ContainerInterface;
use Transport\Service\RateManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleRecountShippingCostFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $rateManager = $container->get(RateManager::class);
        /** @var SaleWagonManager $saleWagonManager */
        $saleWagonManager = $container->get(SaleWagonManager::class);
        $taxManager = $container->get(TaxManager::class);
        return new SaleRecountShippingCost($rateManager, $saleWagonManager, $taxManager);
    }

}