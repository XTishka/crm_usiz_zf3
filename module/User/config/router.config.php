<?php

namespace User;

return [
    'routes' => [
        'user' => [
            'type'          => 'segment',
            'options'       => [
                'route'    => '/user',
                'defaults' => [
                    'controller' => Controller\AccountController::class,
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes'  => [
                'edit'   => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/edit[/:id]',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                        'defaults'    => [
                            'action' => 'edit',
                        ],
                    ],
                ],
                'delete' => [
                    'type'    => 'segment',
                    'options' => [
                        'route'       => '/delete/:id',
                        'constraints' => [
                            'id' => '\d+',
                        ],
                        'defaults'    => [
                            'action' => 'delete',
                        ],
                    ],
                ],
            ],
        ],
        'login'  => [
            'type'    => 'segment',
            'options' => [
                'route'    => '/login',
                'defaults' => [
                    'controller' => Controller\AuthController::class,
                    'action'     => 'login',
                ],
            ],
        ],
        'logout' => [
            'type'    => 'segment',
            'options' => [
                'route'    => '/logout',
                'defaults' => [
                    'controller' => Controller\AuthController::class,
                    'action'     => 'logout',
                ],
            ],
        ],
    ],
];