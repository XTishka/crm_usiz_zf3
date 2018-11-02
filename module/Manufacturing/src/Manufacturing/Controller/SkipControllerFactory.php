<?php

namespace Manufacturing\Controller;

use Contractor\Service\ContractorCompanyManager;
use Manufacturing\Form;
use Manufacturing\Service\FurnaceManager;
use Interop\Container\ContainerInterface;
use Manufacturing\Service\SkipManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SkipControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $furnaceManager = $container->get(FurnaceManager::class);
        $skipManager = $container->get(SkipManager::class);
        $skipCommonForm = $container->get('FormElementManager')->get(Form\SkipCommon::class);
        $companyManager = $container->get(ContractorCompanyManager::class);
        return new SkipController($furnaceManager, $skipManager, $skipCommonForm, $companyManager);
    }

}