<?php

namespace Contractor\Service;

use Interop\Container\ContainerInterface;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContractorAbstractManagerFactory implements AbstractFactoryInterface {

    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, ContractorAbstractManager::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $shortRequestedName = (new ReflectionClass($requestedName))->getShortName();

        $repositoryClass = preg_replace('/^([A-Za-z]+)Manager$/', 'Contractor\Service\Repository\Database${1}', $shortRequestedName);

        /** @var Repository\DatabaseContractorAbstract $databaseContractorRepository */
        $databaseContractorRepository = $container->get($repositoryClass);

        /** @var ContractorAbstractManager $manager */
        $manager = new $requestedName($databaseContractorRepository);

        return $manager;
    }


}