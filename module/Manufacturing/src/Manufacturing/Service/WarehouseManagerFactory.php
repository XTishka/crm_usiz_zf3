<?php

namespace Manufacturing\Service;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $warehouseDbRepository = $container->get(Repository\WarehouseDb::class);
        return new WarehouseManager($warehouseDbRepository);
    }

}