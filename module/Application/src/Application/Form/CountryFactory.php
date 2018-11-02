<?php

namespace Application\Form;

use Application\Domain;
use Application\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class CountryFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new Country();
        $form->setHydrator($container->get('HydratorManager')->get(Hydrator\ClassMethods::class));
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\Country::class));
        $form->setObject(new Domain\CountryEntity());
        return $form;
    }


}