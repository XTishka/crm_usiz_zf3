<?php

namespace Contractor\Form\Element;

use Contractor\Service\ContractorAbstractManager;
use ReflectionClass;
use Interop\Container\ContainerInterface;
use Zend\Form\Element\Select;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContractorSelectFactory implements AbstractFactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, Select::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return object|Select
     * @throws \ReflectionException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $shortRequestedName = (new ReflectionClass($requestedName))->getShortName();
        $managerClassName = preg_replace('/^([A-Za-z]+)Select$/', 'Contractor\Service\\\${1}Manager', $shortRequestedName);

        /** @var ContractorAbstractManager $contractorAbstractManager */
        $contractorAbstractManager = $container->get($managerClassName);

        /** @var Select $select */
        $select = new $requestedName();
        $select->setValueOptions($contractorAbstractManager->getContractorsValueOptions());
        $select->setEmptyOption('Select contractor');

        return $select;
    }

}