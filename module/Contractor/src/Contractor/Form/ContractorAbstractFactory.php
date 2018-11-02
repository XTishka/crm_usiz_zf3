<?php

namespace Contractor\Form;

use Contractor\Entity;
use Contractor\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use ReflectionClass;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContractorAbstractFactory implements AbstractFactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, ContractorAbstract::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return ContractorAbstract|object
     * @throws \ReflectionException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);

        /** @var InputFilter\Contractor $inputFilter */
        $inputFilter = $container->get('InputFilterManager')->get(InputFilter\Contractor::class);

        /** @var Entity\ContractorAbstract $object */
        $object = Entity\ContractorFactory::build((new ReflectionClass($requestedName))->getShortName());

        /** @var ContractorAbstract $form */
        $form = new $requestedName;
        $form->setHydrator($hydrator);
        $form->setInputFilter($inputFilter);
        $form->setObject($object);
        return $form;
    }


}