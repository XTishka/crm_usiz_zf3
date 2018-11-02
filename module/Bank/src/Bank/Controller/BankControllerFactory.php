<?php

namespace Bank\Controller;

use Bank\Form\BankForm;
use Bank\Service\BankManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return BankController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var BankForm $bankForm */
        $bankForm = $container->get('FormElementManager')->get(BankForm::class);
        /** @var BankManager $bankManager */
        $bankManager = $container->get(BankManager::class);
        /** @var BankController $controller */
        $controller = new BankController($bankForm, $bankManager);
        return $controller;
    }

}