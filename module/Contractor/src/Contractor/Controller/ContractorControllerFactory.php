<?php

namespace Contractor\Controller;

use ReflectionClass;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContractorControllerFactory implements AbstractFactoryInterface {

    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, AbstractActionController::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $shortRequestedName = (new ReflectionClass($requestedName))->getShortName();
        $contractAbstractForm = $container->get('FormElementManager')->get(preg_replace('/^([A-Za-z]+)Controller$/', 'Contractor\Form\\\${1}', $shortRequestedName));
        $contractAbstractManager = $container->get(preg_replace('/^([A-Za-z]+)Controller$/', 'Contractor\Service\\\${1}Manager', $shortRequestedName));
        $controller = new $requestedName($contractAbstractForm, $contractAbstractManager);
        return $controller;
    }


}