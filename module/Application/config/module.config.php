<?php

namespace Application;

use Zend\Mvc\Controller\LazyControllerAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'navigation'      => require_once __DIR__ . '/navigation.config.php',
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\Api\Plant\AccountPayableController::class => LazyControllerAbstractFactory::class,
            Controller\Api\AccountPayableController::class       => LazyControllerAbstractFactory::class,
            Controller\Api\CharterCapitalController::class       => LazyControllerAbstractFactory::class,
            Controller\Api\CustomerReceivableController::class   => LazyControllerAbstractFactory::class,
            Controller\Api\DebtToCarrierController::class        => LazyControllerAbstractFactory::class,
            Controller\Api\DebtToOtherController::class          => LazyControllerAbstractFactory::class,
            Controller\Api\DebtToPlantController::class          => LazyControllerAbstractFactory::class,
            Controller\Api\DebtToProviderController::class       => LazyControllerAbstractFactory::class,
            Controller\Api\PrepayFromCustomerController::class   => LazyControllerAbstractFactory::class,
            Controller\Api\PrepayToCarrierController::class      => LazyControllerAbstractFactory::class,
            Controller\Api\PrepayToOtherController::class        => LazyControllerAbstractFactory::class,
            Controller\Api\PrepayToPlantController::class        => LazyControllerAbstractFactory::class,
            Controller\Api\PrepayToProviderController::class     => LazyControllerAbstractFactory::class,
            Controller\Api\TotalReceivableController::class      => LazyControllerAbstractFactory::class,
            /* -------------------------------------------------------------------------------------------------------- */
            Controller\ApiController::class                      => LazyControllerAbstractFactory::class,
            Controller\DashboardController::class                => LazyControllerAbstractFactory::class,
            Controller\PlantDashboardController::class           => LazyControllerAbstractFactory::class,
            Controller\CountryController::class                  => Controller\CountryControllerFactory::class,
            Controller\TaxController::class                      => Controller\TaxControllerFactory::class,
            Controller\ResetController::class                    => LazyControllerAbstractFactory::class,
            /* -------------------------------------------------------------------------------------------------------- */
            Controller\TestController::class                     => Controller\TestControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'factories' => [
            Form\Element\CountrySelect::class    => Form\Element\CountrySelectFactory::class,
            Form\Element\CurrentTaxNumber::class => Form\Element\CurrentTaxNumberFactory::class,
            Form\Element\TaxSelect::class        => Form\Element\TaxSelectFactory::class,
            Form\Fieldset\Address::class         => Form\Fieldset\AddressFactory::class,
            Form\Fieldset\Person::class          => Form\Fieldset\PersonFactory::class,
            Form\Fieldset\Phone::class           => Form\Fieldset\PhoneFactory::class,
            Form\Fieldset\Email::class           => Form\Fieldset\EmailFactory::class,
            Form\Country::class                  => Form\CountryFactory::class,
            Form\Tax::class                      => Form\TaxFactory::class,
        ],
    ],
    'hydrators'       => [
        'factories' => [
            Hydrator\ValueObject::class => InvokableFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\Country::class => InvokableFactory::class,
            InputFilter\Tax::class     => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\Repository\ApiDb::class                  => Service\Repository\ApiDbFactory::class,
            Service\Repository\CountryDb::class              => Service\Repository\CountryDbFactory::class,
            Service\Repository\TaxDb::class                  => Service\Repository\TaxDbFactory::class,
            Service\CountryManager::class                    => Service\CountryManagerFactory::class,
            Service\TaxManager::class                        => Service\TaxManagerFactory::class,
            /* -------------------------------------------------------------------------------------------------------- */
            //Model\AccountsPayableService::class            => Model\AccountsPayableServiceFactory::class,
            Model\AccountsReceivableService::class           => Model\AccountsReceivableServiceFactory::class,
            Model\CarriersReceivableService::class           => Model\CarriersReceivableServiceFactory::class,
            Model\CheckingAccountService::class              => Model\CheckingAccountServiceFactory::class,
            Model\CustomerPayableService::class              => Model\CustomerPayableServiceFactory::class,
            Model\ProvidersReceivableService::class          => Model\ProvidersReceivableServiceFactory::class,
            Model\PurchaseWagonsService::class               => Model\PurchaseWagonsServiceFactory::class,
            Model\SaleWagonsService::class                   => Model\SaleWagonsServiceFactory::class,
            /* -------------------------------------------------------------------------------------------------------- */
            Model\Finance\Plant\AccountPayableService::class => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\DebtToPlantService::class          => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\DebtToProviderService::class       => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\DebtToCarrierService::class        => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\DebtToOtherService::class          => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\PrepayFromCustomerService::class   => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\AccountPayableService::class       => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\TotalReceivableService::class      => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\CharterCapitalService::class       => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\CustomerReceivableService::class   => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\PrepayToOtherService::class        => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\PrepayToProviderService::class     => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\PrepayToPlantService::class        => Model\Finance\AbstractServiceFactory::class,
            Model\Finance\PrepayToCarrierService::class      => Model\Finance\AbstractServiceFactory::class,
            /* -------------------------------------------------------------------------------------------------------- */
        ],
    ],
    'translator'      => [
        'locale'                    => 'ru_RU',
        'translation_file_patterns' => [
            [
                'type'     => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
            ],
        ],
    ],
    'view_manager'    => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'error/403'         => __DIR__ . '/../view/error/403.phtml',
            'error/404'         => __DIR__ . '/../view/error/404.phtml',
            'error/index'       => __DIR__ . '/../view/error/index.phtml',
            'layout/admin'      => __DIR__ . '/../view/layout/admin.phtml',
            'layout/layout'     => __DIR__ . '/../view/layout/layout.phtml',
            'partial/menu'      => __DIR__ . '/../view/partial/menu.phtml',
            'partial/messenger' => __DIR__ . '/../view/partial/messenger.phtml',
            'partial/paginator' => __DIR__ . '/../view/partial/paginator.phtml',
        ],
        'template_path_stack'      => [
            __DIR__ . '/../view',
        ],
        'strategies'               => ['ViewJsonStrategy'],

    ],
    'view_helpers'    => [
        'aliases'   => [
            'currencyFormat' => View\Helper\CurrencyFormat::class,
        ],
        'factories' => [
            View\Helper\CurrencyFormat::class => InvokableFactory::class,
        ],
    ],
    'validators'      => [
        'aliases'   => [
            'AppDbNoRecordExists' => Validator\Db\NoRecordExists::class,
        ],
        'factories' => [
            Validator\Db\NoRecordExists::class => InvokableFactory::class,
        ],
    ],
];