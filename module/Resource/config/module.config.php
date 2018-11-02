<?php

namespace Resource;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\DropoutController::class  => Controller\DropoutControllerFactory::class,
            Controller\MaterialController::class => Controller\MaterialControllerFactory::class,
            Controller\ProductController::class  => Controller\ProductControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'aliases'   => [

        ],
        'factories' => [
            Form\Element\MaterialSelect::class => Form\Element\MaterialSelectFactory::class,
            Form\Element\ProductSelect::class  => Form\Element\ProductSelectFactory::class,
            Form\Fieldset\Fraction::class      => Form\Fieldset\FractionFactory::class,
            Form\DropoutForm::class            => Form\DropoutFormFactory::class,
            Form\Material::class               => Form\MaterialFactory::class,
            Form\Product::class                => Form\ProductFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\Material::class => InvokableFactory::class,
            InputFilter\Product::class  => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\DropoutManager::class        => Service\DropoutManagerFactory::class,
            Service\Repository\MaterialDb::class => Service\Repository\MaterialDbFactory::class,
            Service\Repository\ProductDb::class  => Service\Repository\ProductDbFactory::class,
            Service\MaterialManager::class       => Service\MaterialManagerFactory::class,
            Service\ProductManager::class        => Service\ProductManagerFactory::class,
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