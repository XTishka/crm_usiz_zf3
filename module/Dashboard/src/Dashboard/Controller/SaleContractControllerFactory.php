<?php

namespace Dashboard\Controller;

use Application\Model\SaleWagonsService;
use Contractor\Service\ContractorCompanyManager;
use Document\Form;
use Document\Service\SaleContractManager;
use Document\Service\SaleWagonManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleContractControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $companyManager = $container->get(ContractorCompanyManager::class);
        $saleContractManager = $container->get(SaleContractManager::class);
        $saleWagonManager = $container->get(SaleWagonManager::class);
        $saleWagonsService = $container->get(SaleWagonsService::class);
        $saleContractForm = $container->get('FormElementManager')->get(Form\SaleContract::class);
        $saleWagonFilterForm = $container->get('FormElementManager')->get(Form\SaleWagonFilter::class);
        return new SaleContractController($companyManager, $saleContractManager, $saleWagonManager, $saleWagonsService, $saleContractForm, $saleWagonFilterForm);
    }

}