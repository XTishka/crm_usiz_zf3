<?php

namespace Application\Controller;

use Application\Form;
use Application\Service\TaxManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TaxControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $taxManager = $container->get(TaxManager::class);
        $taxForm = $container->get('FormElementManager')->get(Form\Tax::class);
        return new TaxController($taxManager, $taxForm);
    }


}