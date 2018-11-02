<?php

namespace User\Service;

use Interop\Container\ContainerInterface;
use User\Service\Repository\DatabaseAccount;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AccountManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $repository = $container->get(DatabaseAccount::class);
        $manager = new AccountManager($repository);
        return $manager;
    }

}