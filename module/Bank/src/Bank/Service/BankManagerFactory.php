<?php

namespace Bank\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Adapter $adapter */
        $adapter = $container->get(Adapter::class);

        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        return new BankManager($adapter, $hydrator);
    }

}