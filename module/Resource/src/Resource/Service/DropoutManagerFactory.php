<?php

namespace Resource\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class DropoutManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Adapter $adapter */
        $adapter = $container->get(Adapter::class);

        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);

        $hydrator->addFilter('provider_name', new MethodMatchFilter('getProviderName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('material_name', new MethodMatchFilter('getMaterialName'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('period_begin', new DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('period_end', new DateTimeFormatterStrategy('Y-m-d'));

        return new DropoutManager($adapter, $hydrator);
    }

}