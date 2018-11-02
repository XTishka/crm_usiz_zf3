<?php

namespace Dashboard\Controller;

use Application\Model\PurchaseWagonsService;
use Contractor\Service\ContractorCompanyManager;
use Document\Form;
use Document\Service\PurchaseContractManager;
use Document\Service\PurchaseWagonManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseContractControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $companyManager = $container->get(ContractorCompanyManager::class);
        $purchaseContractManager = $container->get(PurchaseContractManager::class);
        $purchaseWagonManager = $container->get(PurchaseWagonManager::class);
        $purchaseWagonsService = $container->get(PurchaseWagonsService::class);
        $purchaseContractForm = $container->get('FormElementManager')->get(Form\PurchaseContract::class);
        $purchaseWagonFilterForm = $container->get('FormElementManager')->get(Form\PurchaseWagonFilter::class);

        return new PurchaseContractController(
            $companyManager,
            $purchaseContractManager,
            $purchaseWagonManager,
            $purchaseWagonsService,
            $purchaseContractForm,
            $purchaseWagonFilterForm
        );
    }

}