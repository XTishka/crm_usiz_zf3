<?php

namespace Transport\Form;

use Transport\Domain;
use Transport\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class StationFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Station();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Station::class));
        $form->setObject(new Domain\StationEntity());
        return $form;
    }

}