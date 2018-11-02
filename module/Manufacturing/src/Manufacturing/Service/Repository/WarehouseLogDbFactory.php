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

class WarehouseLogDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addFilter('warehouse_name', new MethodMatchFilter('getWarehouseName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('resource_name', new MethodMatchFilter('getResourceName'), FilterComposite::CONDITION_AND);
        $hydrator->addFilter('contractor_name', new MethodMatchFilter('getContractorName'), FilterComposite::CONDITION_AND);

        $hydrator->addStrategy('log_id', new IntegerStrategy());
        $hydrator->addStrategy('warehouse_id', new IntegerStrategy());
        $hydrator->addStrategy('resource_id', new IntegerStrategy());
        $hydrator->addStrategy('contractor_id', new IntegerStrategy());

        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\WarehouseEntity $prototype */
        $prototype = new Domain\WarehouseLogEntity();

        return new WarehouseLogDb($dbAdapter, $hydrator, $prototype);
    }

}