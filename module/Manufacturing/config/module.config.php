<?php

namespace Manufacturing;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\FurnaceController::class   => Controller\FurnaceControllerFactory::class,
            Controller\SkipController::class      => Controller\SkipControllerFactory::class,
            Controller\WarehouseController::class => Controller\WarehouseControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'aliases'   => [
            'FurnaceSelect'   => Form\Element\FurnaceSelect::class,
            'PlantSelect'     => Form\Element\PlantSelect::class,
            'WarehouseSelect' => Form\Element\WarehouseSelect::class,
        ],
        'factories' => [
            Form\Element\FurnaceSelect::class           => Form\Element\FurnaceSelectFactory::class,
            Form\Element\PlantSelect::class             => Form\Element\PlantSelectFactory::class,
            Form\Element\WarehouseSelect::class         => Form\Element\WarehouseSelectFactory::class,
            Form\Element\WarehouseBalancesSelect::class => Form\Element\WarehouseBalancesSelectFactory::class,
            Form\Element\WarehouseTypeSelect::class     => InvokableFactory::class,
            Form\Fieldset\SkipMaterial::class           => Form\Fieldset\SkipMaterialFactory::class,
            Form\Furnace::class                         => Form\FurnaceFactory::class,
            Form\FurnaceSkip::class                     => Form\FurnaceSkipFactory::class,
            Form\Warehouse::class                       => Form\WarehouseFactory::class,
            Form\SkipCommon::class                      => Form\SkipCommonFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\Furnace::class     => InvokableFactory::class,
            InputFilter\FurnaceSkip::class => InvokableFactory::class,
            InputFilter\Warehouse::class   => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\Repository\FurnaceDb::class      => Service\Repository\FurnaceDbFactory::class,
            Service\Repository\FurnaceLogDb::class   => Service\Repository\FurnaceLogDbFactory::class,
            Service\Repository\WarehouseDb::class    => Service\Repository\WarehouseDbFactory::class,
            Service\Repository\WarehouseLogDb::class => Service\Repository\WarehouseLogDbFactory::class,
            Service\Repository\DatabaseSkip::class   => Service\Repository\DatabaseSkipFactory::class,
            Service\FurnaceManager::class            => Service\FurnaceManagerFactory::class,
            Service\FurnaceSkipManager::class        => Service\FurnaceSkipManagerFactory::class,
            Service\WarehouseManager::class          => Service\WarehouseManagerFactory::class,
            Service\WarehouseLogManager::class       => Service\WarehouseLogManagerFactory::class,
            Service\SkipManager::class               => Service\SkipManagerFactory::class,
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