<?php

namespace User\Service;

use Interop\Container\ContainerInterface;
use User\Service\Repository\DatabaseAccount;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class AuthenticationServiceFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AuthenticationService
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $storage = new Session('Auth', 'session', $container->get(SessionManager::class));
        $adapter = new CallbackCheckAdapter($container->get(Adapter::class), DatabaseAccount::TABLE_USER_ACCOUNTS);
        $adapter->setCredentialColumn('password');
        $adapter->setIdentityColumn('email');
        $service = new AuthenticationService($storage, $adapter);
        return $service;
    }


}