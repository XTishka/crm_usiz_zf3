<?php

namespace Contractor\Controller;

use Contractor\Form;
use Contractor\Service\ContractorCommonManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContractorCommonControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $contractorCommonManager = $container->get(ContractorCommonManager::class);
        $contractorCommonForm = $container->get('FormElementManager')->get(Form\ContractorCommon::class);
        $contractorCommonFilterForm = $container->get('FormElementManager')->get(Form\ContractorCommonFilter::class);
        return new ContractorCommonController($contractorCommonForm, $contractorCommonManager, $contractorCommonFilterForm);
    }

}