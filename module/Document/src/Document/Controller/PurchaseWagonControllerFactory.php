<?php

namespace Document\Controller;

use Document\Form;
use Document\Service;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseWagonControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $purchaseContractManager = $container->get(Service\PurchaseContractManager::class);
        $purchaseWagonManager = $container->get(Service\PurchaseWagonManager::class);
        $purchaseEditWagonForm = $container->get('FormElementManager')->get(Form\PurchaseEditWagon::class);
        $purchaseLoadingWagonForm = $container->get('FormElementManager')->get(Form\PurchaseLoadingWagon::class);
        $purchaseUnloadingWagonForm = $container->get('FormElementManager')->get(Form\PurchaseUnloadingWagon::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        return new PurchaseWagonController($purchaseContractManager, $purchaseWagonManager, $purchaseEditWagonForm,
            $purchaseLoadingWagonForm, $purchaseUnloadingWagonForm, $warehouseLogManager);
    }

}