<?php

namespace Transport\Service;

use Interop\Container\ContainerInterface;
use Transport\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class StationManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $stationDbRepository = $container->get(Repository\StationDb::class);
        return new StationManager($stationDbRepository);
    }

}