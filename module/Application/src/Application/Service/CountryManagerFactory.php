<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CountryManagerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $countryDbRepository = $container->get(Repository\CountryDb::class);
        return new CountryManager($countryDbRepository);
    }

}