<?php

namespace Manufacturing\Form\Fieldset;

use Application\Hydrator\Strategy\IntegerStrategy;
use Interop\Container\ContainerInterface;
use Manufacturing\Domain\SkipMaterialEntity;
use Zend\Hydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

class SkipMaterialFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var Hydrator\ClassMethods $hydrator */
        $hydrator = $container->get('HydratorManager')->get(Hydrator\ClassMethods::class);
        $hydrator->addStrategy('material_type_id', new IntegerStrategy());
        $hydrator->addStrategy('supplier_id', new IntegerStrategy());
        $hydrator->addStrategy('material_id', new IntegerStrategy());

        $object = new SkipMaterialEntity();

        $fieldset = new SkipMaterial();
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject($object);
        return $fieldset;
    }

}