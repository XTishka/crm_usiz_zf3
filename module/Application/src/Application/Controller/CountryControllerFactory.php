<?php

namespace Application\Controller;

use Application\Form;
use Application\Service\CountryManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CountryControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $countryManager = $container->get(CountryManager::class);
        $countryForm = $container->get('FormElementManager')->get(Form\Country::class);
        return new CountryController($countryManager, $countryForm);
    }


}