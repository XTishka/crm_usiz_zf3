<?php

namespace Document\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseContractManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $purchaseContractDbRepository = $container->get(Repository\PurchaseContractDb::class);
        return new PurchaseContractManager($purchaseContractDbRepository);
    }

}