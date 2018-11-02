<?php

namespace Transport\Service;

use Interop\Container\ContainerInterface;
use Transport\Service\Repository;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;

class RateManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return object|RateManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $rateDbRepository = $container->get(Repository\RateDb::class);
        $sessionManager = $container->get(SessionManager::class);
        return new RateManager($rateDbRepository, $sessionManager);
    }

}