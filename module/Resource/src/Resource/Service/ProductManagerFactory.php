<?php

namespace Resource\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProductManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $productDbRepository = $container->get(Repository\ProductDb::class);
        return new ProductManager($productDbRepository);
    }

}