<?php

namespace Document\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleContractManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $saleContractDbRepository = $container->get(Repository\SaleContractDb::class);
        return new SaleContractManager($saleContractDbRepository);
    }

}