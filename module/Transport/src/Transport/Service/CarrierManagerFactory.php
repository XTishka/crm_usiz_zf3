<?php

namespace Transport\Service;

use Interop\Container\ContainerInterface;
use Transport\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class CarrierManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $carrierDbRepository = $container->get(Repository\CarrierDb::class);
        return new CarrierManager($carrierDbRepository);
    }

}