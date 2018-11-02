<?php

namespace Document\Controller;

use Document\Form;
use Document\Service\PurchaseContractManager;
use Document\Service\PurchaseWagonManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseContractControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $purchaseContractManager = $container->get(PurchaseContractManager::class);
        $purchaseWagonManager = $container->get(PurchaseWagonManager::class);
        $purchaseContractForm = $container->get('FormElementManager')->get(Form\PurchaseContract::class);
        return new PurchaseContractController($purchaseContractManager, $purchaseWagonManager, $purchaseContractForm);
    }

}