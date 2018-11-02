<?php

namespace User\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccessManagerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AccessManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $authenticationService = $container->get(AuthenticationService::class);
        $config = $container->get('config');
        $accessManager = new AccessManager($config, $authenticationService);
        return $accessManager;
    }


}