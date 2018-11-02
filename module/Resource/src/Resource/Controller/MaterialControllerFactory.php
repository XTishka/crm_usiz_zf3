<?php

namespace Resource\Controller;

use Resource\Form;
use Resource\Service\MaterialManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MaterialControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $materialManager = $container->get(MaterialManager::class);
        $materialForm = $container->get('FormElementManager')->get(Form\Material::class);
        return new MaterialController($materialManager, $materialForm);
    }


}