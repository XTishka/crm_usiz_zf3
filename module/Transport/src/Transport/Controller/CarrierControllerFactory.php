<?php

namespace Transport\Controller;

use Transport\Form;
use Transport\Service\CarrierManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CarrierControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $carrierManager = $container->get(CarrierManager::class);
        $carrierForm = $container->get('FormElementManager')->get(Form\Carrier::class);
        return new CarrierController($carrierManager, $carrierForm);
    }


}