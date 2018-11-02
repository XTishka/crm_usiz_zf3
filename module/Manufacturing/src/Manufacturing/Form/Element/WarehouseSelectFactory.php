<?php

namespace Manufacturing\Form\Element;

use Interop\Container\ContainerInterface;
use Manufacturing\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Service\WarehouseManager $stationManager */
        $stationManager = $container->get(Service\WarehouseManager::class);
        $valueOptions = $stationManager->getWarehousesValueOptions();

        $element = new WarehouseSelect();
        $element->setValueOptions($valueOptions);
        return $element;
    }

}