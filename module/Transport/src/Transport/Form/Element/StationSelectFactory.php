<?php

namespace Transport\Form\Element;

use Interop\Container\ContainerInterface;
use Transport\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class StationSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Service\StationManager $stationManager */
        $stationManager = $container->get(Service\StationManager::class);
        $options = $stationManager->getStationsValueOptions();

        $element = new StationSelect();
        $element->setValueOptions($options);
        return $element;
    }

}