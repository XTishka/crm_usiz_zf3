<?php

namespace Application\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Application\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class TaxDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('tax_id', new IntegerStrategy());
        $hydrator->addStrategy('updated', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\TaxEntity $prototype */
        $prototype = new Domain\TaxEntity();

        return new TaxDb($dbAdapter, $hydrator, $prototype);
    }

}