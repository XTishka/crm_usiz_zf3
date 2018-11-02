<?php

namespace Resource\Form;

use Resource\Domain;
use Resource\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProductFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Product();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Product::class));
        $form->setObject(new Domain\ProductEntity());
        return $form;
    }

}