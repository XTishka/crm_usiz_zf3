<?php

namespace Document\Controller;

use Document\Form;
use Document\Service;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleWagonControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $saleContractManager = $container->get(Service\SaleContractManager::class);
        $saleWagonManager = $container->get(Service\SaleWagonManager::class);
        $saleEditWagonForm = $container->get('FormElementManager')->get(Form\SaleEditWagon::class);
        $saleLoadingWagonForm = $container->get('FormElementManager')->get(Form\SaleLoadingWagon::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        return new SaleWagonController($saleContractManager, $saleWagonManager, $saleEditWagonForm,
            $saleLoadingWagonForm, $warehouseLogManager);
    }

}