<?php

namespace Resource\Form;

use Resource\Domain;
use Resource\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class MaterialFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Material();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Material::class));
        $form->setObject(new Domain\MaterialEntity());
        return $form;
    }

}