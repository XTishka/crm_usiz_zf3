<?php

namespace Contractor;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'abstract_factories' => [
            Controller\ContractorControllerFactory::class,
        ],
        'factories'          => [
            Controller\ContractorAdditionalController::class => Controller\ContractorControllerFactory::class,
            Controller\ContractorCarrierController::class    => Controller\ContractorControllerFactory::class,
            Controller\ContractorCompanyController::class    => Controller\ContractorControllerFactory::class,
            Controller\ContractorCorporateController::class  => Controller\ContractorControllerFactory::class,
            Controller\ContractorConsigneeController::class  => Controller\ContractorControllerFactory::class,
            Controller\ContractorCustomerController::class   => Controller\ContractorControllerFactory::class,
            Controller\ContractorPlantController::class      => Controller\ContractorControllerFactory::class,
            Controller\ContractorProviderController::class   => Controller\ContractorControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'abstract_factories' => [
            Form\ContractorAbstractFactory::class,
            Form\Element\ContractorSelectFactory::class,
        ],
        'factories'          => [
            Form\Element\ContractorAdditionalSelect::class => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorCarrierSelect::class    => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorCompanySelect::class    => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorCorporateSelect::class  => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorConsigneeSelect::class  => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorCustomerSelect::class   => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorPlantSelect::class      => Form\Element\ContractorSelectFactory::class,
            Form\Element\ContractorProviderSelect::class   => Form\Element\ContractorSelectFactory::class,
            Form\ContractorAdditional::class               => Form\ContractorAbstractFactory::class,
            Form\ContractorCarrier::class                  => Form\ContractorAbstractFactory::class,
            Form\ContractorCompany::class                  => Form\ContractorAbstractFactory::class,
            Form\ContractorConsignee::class                => Form\ContractorAbstractFactory::class,
            Form\ContractorCorporate::class                => Form\ContractorAbstractFactory::class,
            Form\ContractorCustomer::class                 => Form\ContractorAbstractFactory::class,
            Form\ContractorProvider::class                 => Form\ContractorAbstractFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\Contractor::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            Service\ContractorAbstractManagerFactory::class,
            Service\Repository\DatabaseContractorAbstractFactory::class,
        ],
        'factories'          => [
            Service\ContractorAdditionalManager::class => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorCarrierManager::class    => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorCompanyManager::class    => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorCorporateManager::class  => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorConsigneeManager::class  => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorCustomerManager::class   => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorPlantManager::class      => Service\ContractorAbstractManagerFactory::class,
            Service\ContractorProviderManager::class   => Service\ContractorAbstractManagerFactory::class,
        ],
    ],
    'validators'      => [
        'factories' => [

        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'contractorAdditionalMenu' => View\Helper\ContractorAdditionalMenu::class,
            'contractorCarrierMenu'    => View\Helper\ContractorCarrierMenu::class,
            'contractorCompanyMenu'    => View\Helper\ContractorCompanyMenu::class,
            'contractorCorporateMenu'  => View\Helper\ContractorCorporateMenu::class,
            'contractorCustomerMenu'   => View\Helper\ContractorCustomerMenu::class,
            'contractorPlantMenu'      => View\Helper\ContractorPlantMenu::class,
            'contractorProviderMenu'   => View\Helper\ContractorProviderMenu::class,
        ],
        'factories' => [
            View\Helper\ContractorAdditionalMenu::class => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorCarrierMenu::class    => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorCompanyMenu::class    => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorCorporateMenu::class  => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorCustomerMenu::class   => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorPlantMenu::class      => View\Helper\ContractorAbstractMenuFactory::class,
            View\Helper\ContractorProviderMenu::class   => View\Helper\ContractorAbstractMenuFactory::class,
        ],
    ],

];