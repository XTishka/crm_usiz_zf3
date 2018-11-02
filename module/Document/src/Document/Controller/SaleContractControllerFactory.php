<?php

namespace Document\Controller;

use Document\Form;
use Document\Service\SaleContractManager;
use Document\Service\SaleWagonManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SaleContractControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $saleContractManager = $container->get(SaleContractManager::class);
        $saleWagonManager = $container->get(SaleWagonManager::class);
        $saleContractForm = $container->get('FormElementManager')->get(Form\SaleContract::class);
        return new SaleContractController($saleContractManager, $saleWagonManager, $saleContractForm);
    }

}