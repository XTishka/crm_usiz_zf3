<?php

namespace Reports\Controller;

use Interop\Container\ContainerInterface;
use Reports\Form\PurchaseWagonsFilterForm;
use Transport\Service\RateManager;
use Zend\Db\Adapter\Adapter as DatabaseAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseWagonsControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $formPurchaseWagonsFilter = $container->get('FormElementManager')->get(PurchaseWagonsFilterForm::class);
        $db = $container->get(DatabaseAdapter::class);
        $rateManager = $container->get(RateManager::class);
        return new PurchaseWagonsController($formPurchaseWagonsFilter, $db, $rateManager);
    }

}