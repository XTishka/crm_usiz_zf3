<?php

namespace Contractor\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContractorCommonFilterFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $form = new ContractorCommonFilter();
        return $form;
    }

}