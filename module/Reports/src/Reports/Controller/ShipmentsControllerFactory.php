<?php

namespace Reports\Controller;

use Interop\Container\ContainerInterface;
use Reports\Form\ShipmentsFilterForm;
use Transport\Service\RateManager;
use Zend\Db\Adapter\Adapter as DatabaseAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

class ShipmentsControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $formShipmentsFilter = $container->get('FormElementManager')->get(ShipmentsFilterForm::class);
        $db = $container->get(DatabaseAdapter::class);
        $rateManager = $container->get(RateManager::class);
        return new ShipmentsController($formShipmentsFilter, $db, $rateManager);
    }

}