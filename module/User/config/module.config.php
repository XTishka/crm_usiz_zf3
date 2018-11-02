<?php

namespace User;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'acl'                => require_once __DIR__ . '/acl.config.php',
    'router'             => require_once __DIR__ . '/router.config.php',
    'controllers'        => [
        'factories' => [
            Controller\AccountController::class => Controller\AccountControllerFactory::class,
            Controller\AuthController::class    => Controller\AuthControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'AccessManager' => Controller\Plugin\AccessManager::class,
        ],
        'factories' => [
            Controller\Plugin\AccessManager::class => Controller\Plugin\AccessManagerFactory::class,
        ],
    ],
    'form_elements'      => [
        'factories' => [
            Form\Account::class => Form\AccountFactory::class,
        ],
    ],
    'input_filters'      => [
        'factories' => [
            InputFilter\Account::class => InvokableFactory::class,
        ],
    ],
    'service_manager'    => [
        'factories' => [
            AuthenticationService::class              => Service\AuthenticationServiceFactory::class,
            Service\Repository\DatabaseAccount::class => Service\Repository\DatabaseAccountFactory::class,
            Service\AccountManager::class             => Service\AccountManagerFactory::class,
            Service\AuthManager::class                => Service\AuthManagerFactory::class,
        ],
    ],
    'view_manager'       => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];