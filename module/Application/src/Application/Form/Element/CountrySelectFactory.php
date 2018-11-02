<?php

namespace Application\Form\Element;

use Application\Service\CountryManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CountrySelectFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var CountryManager $countryManager */
        $countryManager = $container->get(CountryManager::class);
        $element = new CountrySelect();
        $element->setValueOptions($countryManager->getCountriesValueOptions());
        return $element;
    }

}