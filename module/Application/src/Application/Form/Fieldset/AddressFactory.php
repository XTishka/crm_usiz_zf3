<?php

namespace Application\Form\Fieldset;

use Application\Domain;
use Application\Hydrator;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AddressFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $fieldset = new Address();
        $fieldset->setHydrator($container->get('HydratorManager')->get(Hydrator\ValueObject::class));
        $fieldset->setObject(Domain\AddressValueObject::factory());
        return $fieldset;
    }

}