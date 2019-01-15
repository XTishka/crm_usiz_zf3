<?php

namespace Reports;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\DailyController::class          => Controller\DailyControllerFactory::class,
            Controller\PurchaseWagonsController::class => Controller\PurchaseWagonsControllerFactory::class,
            Controller\ShipmentsController::class      => Controller\ShipmentsControllerFactory::class,
            Controller\FinanceController::class        => Controller\FinanceControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'invokables' => [
            Form\FinanceFilterForm::class,
            Form\PurchaseWagonsFilterForm::class,
            Form\ShipmentsFilterForm::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [

        ],
    ],
    'service_manager' => [
        'factories' => [

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

        ],
        'factories' => [

        ],
    ],

];