<?php

namespace Manufacturing\Service;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class SkipManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $databaseSkipRepository = $container->get(Repository\DatabaseSkip::class);
        $furnaceDbRepository = $container->get(Repository\FurnaceDb::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        return new SkipManager($databaseSkipRepository, $furnaceDbRepository, $warehouseLogManager);
    }

}