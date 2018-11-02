<?php

namespace Bank\Form;

use Bank\Entity\BankEntity;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\ServiceManager\Factory\FactoryInterface;

class BankFormFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);
        /** @var BankEntity $object */
        $object = new BankEntity();
        /** @var BankForm $form */
        $form = new BankForm();
        $form->setHydrator($hydrator);
        $form->setObject($object);
        return $form;

    }

}