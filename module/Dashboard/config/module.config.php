<?php

namespace Dashboard;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\PurchaseContractController::class => Controller\PurchaseContractControllerFactory::class,
            Controller\PurchaseWagonController::class    => Controller\PurchaseWagonControllerFactory::class,
            Controller\SaleContractController::class     => Controller\SaleContractControllerFactory::class,
            Controller\SaleWagonController::class        => Controller\SaleWagonControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'factories' => [

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
];