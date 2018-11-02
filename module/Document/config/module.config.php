<?php

namespace Document;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\PurchaseContractController::class => Controller\PurchaseContractControllerFactory::class,
            Controller\PurchaseWagonController::class    => Controller\PurchaseWagonControllerFactory::class,
            Controller\SaleContractController::class     => Controller\SaleContractControllerFactory::class,
            Controller\SaleWagonController::class        => Controller\SaleWagonControllerFactory::class,
            Controller\TransactionController::class      => Controller\TransactionControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'abstract_factories' => [
            Form\TransactionAbstractFactory::class,
        ],
        'factories'          => [
            Form\Element\ConditionsSelect::class => InvokableFactory::class,
            Form\Fieldset\Wagon::class           => InvokableFactory::class,
            Form\PurchaseContract::class         => Form\PurchaseContractFactory::class,
            Form\PurchaseEditWagon::class        => Form\PurchaseEditWagonFactory::class,
            Form\PurchaseLoadingWagon::class     => Form\PurchaseLoadingWagonFactory::class,
            Form\PurchaseUnloadingWagon::class   => InvokableFactory::class,
            Form\PurchaseImportWagon::class      => Form\PurchaseImportWagonFactory::class,
            Form\PurchaseWagonFilter::class      => Form\PurchaseWagonFilterFactory::class,
            Form\SaleContract::class             => Form\SaleContractFactory::class,
            Form\SaleWagon::class                => Form\SaleWagonFactory::class,
            Form\SaleLoadingWagon::class         => Form\SaleLoadingWagonFactory::class,
            Form\SaleEditWagon::class            => Form\SaleEditWagonFactory::class,
            Form\SaleWagonFilter::class          => Form\SaleWagonFilterFactory::class,
            Form\SaleImportWagon::class          => Form\SaleImportWagonFactory::class,

            Form\TransactionAdditional::class => Form\TransactionAbstractFactory::class,
            Form\TransactionCarrier::class    => Form\TransactionAbstractFactory::class,
            Form\TransactionCompany::class    => Form\TransactionAbstractFactory::class,
            Form\TransactionCorporate::class  => Form\TransactionAbstractFactory::class,
            Form\TransactionCustomer::class   => Form\TransactionAbstractFactory::class,
            Form\TransactionPlant::class      => Form\TransactionAbstractFactory::class,
            Form\TransactionProvider::class   => Form\TransactionAbstractFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\PurchaseContract::class      => InvokableFactory::class,
            InputFilter\PurchaseWagon::class         => InvokableFactory::class,
            InputFilter\SaleContract::class          => InvokableFactory::class,
            InputFilter\SaleWagon::class             => InvokableFactory::class,
            InputFilter\TransactionAdditional::class => InvokableFactory::class,
            InputFilter\TransactionCarrier::class    => InvokableFactory::class,
            InputFilter\TransactionCompany::class    => InvokableFactory::class,
            InputFilter\TransactionCorporate::class  => InvokableFactory::class,
            InputFilter\TransactionCustomer::class   => InvokableFactory::class,
            InputFilter\TransactionPlant::class      => InvokableFactory::class,
            InputFilter\TransactionProvider::class   => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\Repository\FinanceLogDb::class         => Service\Repository\FinanceLogDbFactory::class,
            Service\Repository\FinanceTransactionDb::class => Service\Repository\FinanceTransactionDbFactory::class,
            Service\Repository\PurchaseContractDb::class   => Service\Repository\PurchaseContractDbFactory::class,
            Service\Repository\PurchaseWagonDb::class      => Service\Repository\PurchaseWagonDbFactory::class,
            Service\Repository\SaleContractDb::class       => Service\Repository\SaleContractDbFactory::class,
            Service\Repository\SaleWagonDb::class          => Service\Repository\SaleWagonDbFactory::class,
            Service\FinanceLogManager::class               => Service\FinanceLogManagerFactory::class,
            Service\FinanceManager::class                  => Service\FinanceManagerFactory::class,
            Service\PurchaseContractManager::class         => Service\PurchaseContractManagerFactory::class,
            Service\PurchaseWagonManager::class            => Service\PurchaseWagonManagerFactory::class,
            Service\SaleContractManager::class             => Service\SaleContractManagerFactory::class,
            Service\SaleWagonManager::class                => Service\SaleWagonManagerFactory::class,
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
];