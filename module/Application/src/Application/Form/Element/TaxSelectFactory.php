<?php

namespace Application\Form\Element;

use Application\Service\TaxManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TaxSelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var TaxManager $taxManager */
        $taxManager = $container->get(TaxManager::class);
        $element = new TaxSelect();
        $element->setValueOptions($taxManager->getTaxesValueOptions());
        return $element;
    }

}