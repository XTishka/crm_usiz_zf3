<?php

namespace Document\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Document\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleWagonDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addFilter('wagons', new MethodMatchFilter('getWagons'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('carrier_name', new MethodMatchFilter('getCarrierName'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('wagon_id', new IntegerStrategy());
        $hydrator->addStrategy('contract_id', new IntegerStrategy());
        $hydrator->addStrategy('carrier_id', new IntegerStrategy());
        $hydrator->addStrategy('rate_id', new IntegerStrategy());
        $hydrator->addStrategy('loading_date', new DateTimeFormatterStrategy('Y-m-d'));
        $hydrator->addStrategy('unloading_date', new DateTimeFormatterStrategy('Y-m-d'));

        /** @var Domain\SaleWagonEntity $prototype */
        $prototype = new Domain\SaleWagonEntity();

        return new SaleWagonDb($dbAdapter, $hydrator, $prototype);
    }

}