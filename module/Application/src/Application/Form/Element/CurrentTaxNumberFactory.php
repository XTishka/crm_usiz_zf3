<?php

namespace Application\Form\Element;

use Application\Service;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CurrentTaxNumberFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var Service\TaxManager $taxManager */
        $taxManager = $container->get(Service\TaxManager::class);

        $element = new CurrentTaxNumber();
        $element->setValue($taxManager->getCurrentTaxValue());
        return $element;
    }


}