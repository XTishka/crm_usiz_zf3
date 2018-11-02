<?php

namespace Transport\Controller;

use Transport\Form;
use Transport\Service\StationManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class StationControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $stationManager = $container->get(StationManager::class);
        $stationForm = $container->get('FormElementManager')->get(Form\Station::class);
        return new StationController($stationManager, $stationForm);
    }


}