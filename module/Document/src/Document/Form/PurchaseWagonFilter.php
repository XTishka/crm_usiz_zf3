<?php

namespace Document\Form;

use Document\Domain\PurchaseWagonEntity;
use Document\Form\Element\ConditionsSelect;
use DateTime;
use Zend\Form\Form;

class PurchaseWagonFilter extends Form {

    public function __construct(array $data) {

        parent::__construct();

        $this->setAttribute('id', 'wagon-filter');

        $this->add([
            'name'       => 'number',
            'type'       => 'text',
            'attributes' => [
                'form' => 'wagon-filter',
            ]
        ]);

        $this->add([
            'name'       => 'carrier',
            'type'       => 'select',
            'attributes' => [
                'form' => 'wagon-filter',
            ],
            'options'    => [
                'empty_option'  => 'Select carrier',
                'value_options' => $data['carriers'] ?? [],
            ],
        ]);

        $this->add([
            'type'       => 'select',
            'name'       => 'status',
            'attributes' => [
                'form' => 'wagon-filter',
            ],
            'options'    => [
                'empty_option'  => 'Select status',
                'value_options' => [
                    PurchaseWagonEntity::STATUS_LOADED   => 'Loaded',
                    PurchaseWagonEntity::STATUS_UNLOADED => 'Unloaded',
                ],
            ],
        ]);

        $this->add([
            'type'       => ConditionsSelect::class,
            'name'       => 'conditions',
            'attributes' => [
                'form' => 'wagon-filter',
            ],
            'options'    => [
                'empty_option' => 'Select conditions',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'loading_date',
            'attributes' => [
                'form'          => 'wagon-filter',
                'data-date-min' => (new DateTime($data['minLoadingDate']))->format('c') ?? null,
                'data-date-max' => (new DateTime($data['maxLoadingDate']))->format('c') ?? null,
                'placeholder'   => 'Loading date',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'unloading_date',
            'attributes' => [
                'form'          => 'wagon-filter',
                'data-date-min' => (new DateTime($data['minUnloadingDate']))->format('c') ?? null,
                'data-date-max' => (new DateTime($data['maxUnloadingDate']))->format('c') ?? null,
                'placeholder'   => 'Unloading date',
            ],
        ]);

    }

}