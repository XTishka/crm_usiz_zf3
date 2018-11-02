<?php

namespace Document\Service;

use Document\Service\Repository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FinanceLogManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $financeLogDbRepository = $container->get(Repository\FinanceLogDb::class);
        return new FinanceLogManager($financeLogDbRepository);
    }

}