<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 */

namespace Contractor\Controller;


use Contractor\Form\ContractorCommonFilter;
use Contractor\Service\ContractorCommonManager;
use Interop\Container\ContainerInterface;

class ContractorCommonControllerFactory {

    public function __invoke(ContainerInterface $container) {
        $form = $container->get('FormElementManager')->get(ContractorCommonFilter::class);
        $manger = $container->get(ContractorCommonManager::class);
        return new ContractorCommonController($form, $manger);
    }

}