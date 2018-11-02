<?php

namespace Manufacturing\Service;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceSkipManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $furnaceDb = $container->get(Repository\FurnaceDb::class);
        $furnaceLogDbRepository = $container->get(Repository\FurnaceLogDb::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        return new FurnaceSkipManager($furnaceDb, $furnaceLogDbRepository, $warehouseLogManager);
    }

}