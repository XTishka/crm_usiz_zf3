<?php

namespace Manufacturing\Service;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $furnaceDbRepository = $container->get(Repository\FurnaceDb::class);
        return new FurnaceManager($furnaceDbRepository);
    }

}