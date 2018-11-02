<?php

namespace Resource\Form\Element;

use Interop\Container\ContainerInterface;
use Resource\Service\ProductManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProductSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var ProductManager $productManager */
        $productManager = $container->get(ProductManager::class);

        $element = new ProductSelect();
        $element->setValueOptions($productManager->getProductsValueOptions());
        $element->setEmptyOption('Select product option');
        return $element;
    }


}