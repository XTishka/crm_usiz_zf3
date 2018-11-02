<?php

namespace Transport\Controller;

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
        return new RateController($rateManager, $addRateForm, $rateForm, $rateFilterForm);
    }


}