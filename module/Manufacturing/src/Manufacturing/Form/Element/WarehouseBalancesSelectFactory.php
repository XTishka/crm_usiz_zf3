<?php

namespace Manufacturing\Form\Element;

use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseBalancesSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var WarehouseLogManager $warehouseLogManager */
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        $element = new WarehouseBalancesSelect();
        $element->setValueOptions($warehouseLogManager->getMaterialValueOptions());
        return $element;
    }


}