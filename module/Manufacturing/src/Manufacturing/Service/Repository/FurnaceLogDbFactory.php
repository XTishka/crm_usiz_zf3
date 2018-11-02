<?php

namespace Manufacturing\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Manufacturing\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceLogDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addFilter('materials', new MethodMatchFilter('getMaterials'), FilterComposite::CONDITION_AND);
        $hydrator->addStrategy('date', new DateTimeFormatterStrategy('Y-m-d'));

        /** @var Domain\FurnaceEntity $prototype */
        $prototype = new Domain\FurnaceSkipEntity();

        return new FurnaceLogDb($dbAdapter, $hydrator, $prototype);
    }

}