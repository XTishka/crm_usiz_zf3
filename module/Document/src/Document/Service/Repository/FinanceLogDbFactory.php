<?php

namespace Document\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Document\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class FinanceLogDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addStrategy('log_id', new IntegerStrategy());
        $hydrator->addStrategy('contractor_id', new IntegerStrategy());
        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d'));

        /** @var Domain\PurchaseWagonEntity $prototype */
        $prototype = new Domain\FinanceLogEntity();

        return new FinanceLogDb($dbAdapter, $hydrator, $prototype);
    }

}