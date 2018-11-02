<?php

namespace Document\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseImportWagonFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new PurchaseImportWagon();
        return $form;
    }

}