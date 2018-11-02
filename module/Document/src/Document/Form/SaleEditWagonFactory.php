<?php

namespace Document\Form;

use Document\Domain;
use Document\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleEditWagonFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('loading_date', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));
        $hydrator->addStrategy('unloading_date', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));

        $form = new SaleEditWagon();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\SaleWagon::class));
        $form->setObject(new Domain\SaleWagonEntity());
        return $form;
    }

}