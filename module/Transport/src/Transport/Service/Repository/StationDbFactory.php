<?php

namespace Transport\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Application\Hydrator\Strategy\StringTrimStrategy;
use Interop\Container\ContainerInterface;
use Transport\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class StationDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('station_id', new IntegerStrategy());
        $hydrator->addStrategy('description', new StringTrimStrategy());
        $hydrator->addStrategy('updated', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\StationEntity $prototype */
        $prototype = new Domain\StationEntity();

        return new StationDb($dbAdapter, $hydrator, $prototype);
    }

}