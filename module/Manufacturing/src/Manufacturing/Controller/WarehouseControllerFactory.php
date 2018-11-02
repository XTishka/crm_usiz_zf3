<?php

namespace Manufacturing\Controller;

use Manufacturing\Form;
use Manufacturing\Service\WarehouseManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class WarehouseControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $warehouseManager = $container->get(WarehouseManager::class);
        $warehouseForm = $container->get('FormElementManager')->get(Form\Warehouse::class);
        return new WarehouseController($warehouseManager, $warehouseForm);
    }


}