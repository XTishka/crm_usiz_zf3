<?php

namespace Resource\Controller;

use Resource\Form\DropoutForm;
use Resource\Service\DropoutManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DropoutControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return DropoutController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var DropoutForm $dropoutForm */
        $dropoutForm = $container->get('FormElementManager')->get(DropoutForm::class);
        /** @var DropoutManager $dropoutManager */
        $dropoutManager = $container->get(DropoutManager::class);
        /** @var DropoutController $controller */
        $controller = new DropoutController($dropoutForm, $dropoutManager);
        return $controller;
    }

}