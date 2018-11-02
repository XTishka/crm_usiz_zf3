<?php

namespace Manufacturing\Form;

use Manufacturing\Domain;
use Manufacturing\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Warehouse();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Warehouse::class));
        $form->setObject(new Domain\WarehouseEntity());
        return $form;
    }

}