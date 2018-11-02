<?php

namespace Document\Service;

use Document\Service\Repository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FinanceManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $financeTransactionDbRepository = $container->get(Repository\FinanceTransactionDb::class);
        return new FinanceManager($financeTransactionDbRepository);
    }

}