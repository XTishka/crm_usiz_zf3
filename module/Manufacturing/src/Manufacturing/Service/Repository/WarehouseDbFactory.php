<?php

namespace Manufacturing\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Manufacturing\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addFilter('plant_name', new MethodMatchFilter('getPlantName'), FilterComposite::CONDITION_AND);
        $hydrator->addStrategy('warehouse_id', new IntegerStrategy());
        $hydrator->addStrategy('plant_id', new IntegerStrategy());
        $hydrator->addStrategy('updated', new DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\WarehouseEntity $prototype */
        $prototype = new Domain\WarehouseEntity();

        return new WarehouseDb($dbAdapter, $hydrator, $prototype);
    }

}