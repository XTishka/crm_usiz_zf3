<?php


namespace User\Controller;

use Interop\Container\ContainerInterface;
use User\Form;
use User\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AccountController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $authManager = $container->get(Service\AuthManager::class);
        $loginForm = $container->get('FormElementManager')->get(Form\Login::class);
        $controller = new AuthController($authManager, $loginForm);
        return $controller;
    }

}