<?php

namespace Application\Model;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProvidersReceivableServiceFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $db = $container->get(Adapter::class);
        return new ProvidersReceivableService($db);
    }

}