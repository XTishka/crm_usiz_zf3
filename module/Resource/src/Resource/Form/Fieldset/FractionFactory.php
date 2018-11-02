<?php

namespace Resource\Form\Fieldset;

use Application\Hydrator;
use Interop\Container\ContainerInterface;
use Resource\Domain\FractionValueObject;
use Zend\ServiceManager\Factory\FactoryInterface;

class FractionFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $fieldset = new Fraction();
        $fieldset->setHydrator($container->get('HydratorManager')->get(Hydrator\ValueObject::class));
        $fieldset->setObject(FractionValueObject::factory());
        return $fieldset;
    }

}