<?php

namespace Bank;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\BankController::class        => Controller\BankControllerFactory::class,
            Controller\RecordController::class      => Controller\RecordControllerFactory::class,
            Controller\PlantRecordController::class => Controller\PlantRecordControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'aliases'   => [
            'BankSelect' => Form\Element\BankSelectElement::class,
            'bankSelect' => Form\Element\BankSelectElement::class,
        ],
        'factories' => [
            Form\Element\BankSelectElement::class => Form\Element\BankSelectElementFactory::class,
            Form\BankForm::class                  => Form\BankFormFactory::class,
            Form\RecordForm::class                => Form\RecordFormFactory::class,
            Form\ImportForm::class                => InvokableFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [

        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\BankManager::class   => Service\BankManagerFactory::class,
            Service\RecordManager::class => Service\RecordManagerFactory::class,
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