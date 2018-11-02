<?php

namespace Dashboard\Controller;

use Contractor\Service\ContractorCompanyManager;
use Document\Form;
use Document\Service;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseWagonControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $companyManager = $container->get(ContractorCompanyManager::class);
        $purchaseContractManager = $container->get(Service\PurchaseContractManager::class);
        $purchaseWagonManager = $container->get(Service\PurchaseWagonManager::class);
        $purchaseEditWagonForm = $container->get('FormElementManager')->get(Form\PurchaseEditWagon::class);
        $purchaseLoadingWagonForm = $container->get('FormElementManager')->get(Form\PurchaseLoadingWagon::class);
        $purchaseUnloadingWagonForm = $container->get('FormElementManager')->get(Form\PurchaseUnloadingWagon::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        $importWagonForm = $container->get('FormElementManager')->get(Form\PurchaseImportWagon::class);
        return new PurchaseWagonController($companyManager, $purchaseContractManager, $purchaseWagonManager, $purchaseEditWagonForm,
            $purchaseLoadingWagonForm, $purchaseUnloadingWagonForm, $warehouseLogManager, $importWagonForm);
    }

}