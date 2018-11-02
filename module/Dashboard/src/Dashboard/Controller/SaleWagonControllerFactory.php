<?php

namespace Dashboard\Controller;

use Contractor\Service\ContractorCompanyManager;
use Document\Form;
use Document\Service;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\WarehouseLogManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleWagonControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $companyManager = $container->get(ContractorCompanyManager::class);
        $saleContractManager = $container->get(Service\SaleContractManager::class);
        $saleWagonManager = $container->get(Service\SaleWagonManager::class);
        $saleEditWagonForm = $container->get('FormElementManager')->get(Form\SaleEditWagon::class);
        $saleLoadingWagonForm = $container->get('FormElementManager')->get(Form\SaleLoadingWagon::class);
        $warehouseLogManager = $container->get(WarehouseLogManager::class);
        $importWagonForm = $container->get('FormElementManager')->get(Form\SaleImportWagon::class);
        return new SaleWagonController($companyManager, $saleContractManager, $saleWagonManager, $saleEditWagonForm,
            $saleLoadingWagonForm, $warehouseLogManager, $importWagonForm);
    }

}