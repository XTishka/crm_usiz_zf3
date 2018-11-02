<?php

namespace Document\Form;

use Document\Domain;
use Document\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleContractFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('created', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));
        $form = new SaleContract();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\SaleContract::class));
        $form->setObject(new Domain\SaleContractEntity());
        return $form;
    }

}