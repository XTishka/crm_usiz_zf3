<?php


namespace User\Controller;


use Interop\Container\ContainerInterface;
use User\Form;
use User\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class AccountControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|AccountController
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $accountManager = $container->get(Service\AccountManager::class);
        $accountForm = $container->get('FormElementManager')->get(Form\Account::class);
        $controller = new AccountController($accountManager, $accountForm);
        return $controller;
    }

}