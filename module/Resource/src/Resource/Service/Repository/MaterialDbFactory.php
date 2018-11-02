<?php

namespace Resource\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Resource\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class MaterialDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('material_id', new IntegerStrategy());

        $hydrator->addStrategy('fraction', new Hydrator\Strategy\ClosureStrategy(
            function (Domain\FractionValueObject $object) {
                return $object->toJson();
            },
            function (string $json) {
                return Domain\FractionValueObject::factory($json);
            }
        ));

        $hydrator->addStrategy('updated', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\MaterialEntity $prototype */
        $prototype = new Domain\MaterialEntity();

        return new MaterialDb($dbAdapter, $hydrator, $prototype);
    }

}