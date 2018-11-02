<?php

namespace Contractor\View\Helper;

use Contractor\Service\ContractorAbstractManager;
use Interop\Container\ContainerInterface;
use ReflectionClass;
use Transport\Service\CarrierManager;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ContractorAbstractMenuFactory implements AbstractFactoryInterface {

    public function canCreate(ContainerInterface $container, $requestedName) {
        return is_subclass_of($requestedName, ContractorAbstractMenu::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $shortRequestedName = (new ReflectionClass($requestedName))->getShortName();
        $managerClassName = preg_replace('/^([A-Za-z]+)Menu/', 'Contractor\Service\\\${1}Manager', $shortRequestedName);
        /** @var ContractorAbstractManager $contractorAbstractManager */
        $contractorAbstractManager = $container->get($managerClassName);
        if (ContractorCarrierMenu::class == $requestedName) {
            /** @var CarrierManager $carrierManager */
            $carrierManager = $container->get(CarrierManager::class);
            return new $requestedName($carrierManager->getCarriersValueOptions());
        }
        return new $requestedName($contractorAbstractManager->getContractorsValueOptions());
    }


}