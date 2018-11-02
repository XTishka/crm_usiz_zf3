<?php

namespace Resource\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MaterialManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $materialDbRepository = $container->get(Repository\MaterialDb::class);
        return new MaterialManager($materialDbRepository);
    }

}