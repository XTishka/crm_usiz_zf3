<?php

namespace Document\Service\Repository;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Document\Domain;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Hydrator;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class FinanceTransactionDbFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DbAdapter $dbAdapter */
        $dbAdapter = $container->get(DbAdapter::class);

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('wagon_id', new IntegerStrategy());

        $hydrator->addStrategy('created', new DateTimeFormatterStrategy('Y-m-d H:i:s'));

        /** @var Domain\PurchaseWagonEntity $prototype */
        $prototype = new Domain\TransactionEntity();

        return new FinanceTransactionDb($dbAdapter, $hydrator, $prototype);
    }

}