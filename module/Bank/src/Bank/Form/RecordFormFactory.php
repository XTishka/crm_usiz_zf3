<?php

namespace Bank\Form;

use Bank\Entity\RecordEntity;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class RecordFormFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);
        $hydrator->addStrategy('date', new DateTimeFormatterStrategy('d.m.Y'));
        /** @var RecordEntity $object */
        $object = new RecordEntity();
        /** @var RecordForm $form */
        $form = new RecordForm();
        $form->setHydrator($hydrator);
        $form->setObject($object);
        return $form;

    }

}