<?php

namespace Transport\Form\Element;

use Interop\Container\ContainerInterface;
use Transport\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class CarrierSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Service\CarrierManager $stationManager */
        $stationManager = $container->get(Service\CarrierManager::class);
        $options = $stationManager->getCarriersValueOptions();

        $element = new CarrierSelect();
        $element->setValueOptions($options);

        return $element;
    }

}