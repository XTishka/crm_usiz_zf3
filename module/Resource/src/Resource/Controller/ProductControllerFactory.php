<?php

namespace Resource\Controller;

use Resource\Form;
use Resource\Service\ProductManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProductControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $productManager = $container->get(ProductManager::class);
        $productForm = $container->get('FormElementManager')->get(Form\Product::class);
        return new ProductController($productManager, $productForm);
    }


}