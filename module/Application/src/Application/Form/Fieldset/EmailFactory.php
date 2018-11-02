<?php

namespace Application\Form\Fieldset;

use Application\Domain;
use Application\Hydrator;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmailFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $fieldset = new Email();
        $fieldset->setHydrator($container->get('HydratorManager')->get(Hydrator\ValueObject::class));
        $fieldset->setObject(Domain\EmailValueObject::factory());
        return $fieldset;
    }


}