<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TaxManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $taxDbRepository = $container->get(Repository\TaxDb::class);
        return new TaxManager($taxDbRepository);
    }

}