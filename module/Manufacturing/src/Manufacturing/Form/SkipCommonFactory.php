<?php

namespace Manufacturing\Form;

use Manufacturing\Domain;
use Manufacturing\InputFilter;
use Interop\Container\ContainerInterface;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SkipCommonFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);

        $hydrator->addStrategy('date', new Hydrator\Strategy\DateTimeFormatterStrategy('d.m.Y'));

        $form = new SkipCommon();
        $form->setHydrator($hydrator);
        $form->setInputFilter($container->get('InputFilterManager')->get(InputFilter\FurnaceSkip::class));
        $form->setObject(new Domain\SkipCommonEntity());
        return $form;
    }

}