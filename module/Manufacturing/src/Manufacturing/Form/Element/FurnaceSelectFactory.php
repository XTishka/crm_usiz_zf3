<?php

namespace Manufacturing\Form\Element;

use Interop\Container\ContainerInterface;
use Manufacturing\Service;
use Zend\ServiceManager\Factory\FactoryInterface;

class FurnaceSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Service\FurnaceManager $stationManager */
        $stationManager = $container->get(Service\FurnaceManager::class);
        $options = $stationManager->getFurnacesValueOptions();

        $element = new FurnaceSelect();
        $element->setValueOptions($options);
        return $element;
    }

}