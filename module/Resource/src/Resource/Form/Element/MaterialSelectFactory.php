<?php

namespace Resource\Form\Element;

use Interop\Container\ContainerInterface;
use Resource\Service\MaterialManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class MaterialSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var MaterialManager $materialManager */
        $materialManager = $container->get(MaterialManager::class);

        $element = new MaterialSelect();
        $element->setValueOptions($materialManager->getMaterialsValueOptions());
        return $element;
    }


}