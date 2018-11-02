<?php

namespace Manufacturing\Form;

use Manufacturing\Domain;
use Manufacturing\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Furnace();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Furnace::class));
        $form->setObject(new Domain\FurnaceEntity());
        return $form;
    }

}