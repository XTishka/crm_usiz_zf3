<?php

namespace Manufacturing\Form;

use Manufacturing\Domain;
use Manufacturing\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceSkipFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(ClassMethods::class);

        $hydrator->addStrategy('date', new DateTimeFormatterStrategy('d.m.Y'));

        $form = new FurnaceSkip();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\FurnaceSkip::class));
        $form->setObject(new Domain\FurnaceSkipEntity());
        return $form;
    }


}