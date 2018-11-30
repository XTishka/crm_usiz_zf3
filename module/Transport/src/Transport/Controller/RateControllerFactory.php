<?php

namespace Transport\Controller;

use Document\Service\PurchaseRecountShippingCost;
use Document\Service\SaleRecountShippingCost;
use Transport\Form;
use Transport\Service\RateManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RateControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $rateManager = $container->get(RateManager::class);
        $addRateForm = $container->get('FormElementManager')->get(Form\AddRate::class);
        $rateForm = $container->get('FormElementManager')->get(Form\Rate::class);
        $rateFilterForm = $container->get('FormElementManager')->get(Form\RateFilter::class);
        $purchaseRecountShippingCostService = $container->get(PurchaseRecountShippingCost::class);
        $saleRecountShippingCostService = $container->get(SaleRecountShippingCost::class);
        return new RateController($rateManager, $addRateForm, $rateForm, $rateFilterForm, $purchaseRecountShippingCostService, $saleRecountShippingCostService);
    }


}