<?php

namespace Reports;

return [
    'routes' => [
        'reports' => [
            'type'         => 'segment',
            'options'      => [
                'route'    => '/reports',
                'defaults' => [
                    'action' => 'index',
                ],
            ],
            'child_routes' => [
                'daily'          => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/daily',
                        'defaults' => [
                            'controller' => Controller\DailyController::class,
                        ],
                    ],
                ],
                'shipments'      => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/shipments',
                        'defaults' => [
                            'controller' => Controller\ShipmentsController::class,
                        ],
                    ],
                ],
                'purchaseWagons' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/purchase-wagons',
                        'defaults' => [
                            'controller' => Controller\PurchaseWagonsController::class,
                        ],
                    ],
                ],
                'finance' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'    => '/finance',
                        'defaults' => [
                            'controller' => Controller\FinanceController::class,
                        ],
                    ],
                ],
            ],
        ],
    ],
];