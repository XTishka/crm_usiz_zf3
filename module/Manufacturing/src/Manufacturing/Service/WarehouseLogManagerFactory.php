<?php

namespace Manufacturing\Service;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseLogManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $warehouseLogDbRepository = $container->get(Repository\WarehouseLogDb::class);
        return new WarehouseLogManager($warehouseLogDbRepository);
    }

}