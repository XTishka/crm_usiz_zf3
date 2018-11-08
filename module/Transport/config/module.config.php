<?php

namespace Transport;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router'          => require_once __DIR__ . '/router.config.php',
    'controllers'     => [
        'factories' => [
            Controller\CarrierController::class => Controller\CarrierControllerFactory::class,
            Controller\RateController::class    => Controller\RateControllerFactory::class,
            Controller\StationController::class => Controller\StationControllerFactory::class,
        ],
    ],
    'form_elements'   => [
        'aliases'   => [
            'CarrierTypeSelect' => Form\Element\CarrierTypeSelect::class,
            'StationSelect'     => Form\Element\StationSelect::class,
        ],
        'factories' => [
            Form\Element\CarrierSelect::class       => Form\Element\CarrierSelectFactory::class,
            Form\Element\CarrierTypeSelect::class   => InvokableFactory::class,
            Form\Element\RateDirectionSelect::class => InvokableFactory::class,
            Form\Element\RateSelect::class          => Form\Element\RateSelectFactory::class,
            Form\Element\RateTypeSelect::class      => InvokableFactory::class,
            Form\Element\StationSelect::class       => Form\Element\StationSelectFactory::class,
            Form\Fieldset\RateValue::class          => InvokableFactory::class,
            Form\RateFilter::class                  => Form\RateFilterFactory::class,
            Form\Carrier::class                     => Form\CarrierFactory::class,
            Form\CarrierFilter::class               => Form\CarrierFilterFactory::class,
            Form\AddRate::class                     => Form\AddRateFactory::class,
            Form\Rate::class                        => Form\RateFactory::class,
            Form\Station::class                     => Form\StationFactory::class,
        ],
    ],
    'input_filters'   => [
        'factories' => [
            InputFilter\Carrier::class => InvokableFactory::class,
            InputFilter\Rate::class    => InvokableFactory::class,
            InputFilter\Station::class => InvokableFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\Repository\CarrierDb::class => Service\Repository\CarrierDbFactory::class,
            Service\Repository\RateDb::class    => Service\Repository\RateDbFactory::class,
            Service\Repository\StationDb::class => Service\Repository\StationDbFactory::class,
            Service\CarrierManager::class       => Service\CarrierManagerFactory::class,
            Service\RateManager::class          => Service\RateManagerFactory::class,
            Service\StationManager::class       => Service\StationManagerFactory::class,
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