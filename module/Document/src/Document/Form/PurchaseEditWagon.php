<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorAdditionalSelect;
use Zend\Form\Form;

class PurchaseEditWagon extends Form {

    public function init() {

        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                ],
            ],
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'wagon_id',
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'carrier_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'rate_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select transport rate',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'rate_value_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Tariff grid',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'loading_date',
            'attributes' => [
                'value'            => date('d.m.Y'),
                'data-date-format' => 'dd.mm.yyyy',
            ],
            'options'    => [
                'label' => 'Date of loading the wagon',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'wagon_number',
            'attributes' => [
                'placeholder' => 'Enter number of wagon',
            ],
            'options'    => [
                'label' => 'Number of wagon',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'loading_weight',
            'attributes' => [
                'step'        => 0.001,
                'placeholder' => 'Enter loading weight',
                'value'       => 50,
            ],
            'options'    => [
                'label' => 'Loading weight',
            ],
        ]);

        $this->add([
            'type'    => ContractorAdditionalSelect::class,
            'name'    => 'transport_contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'empty_option'              => 'Select contractor',
                'label'                     => 'Select transport contractor',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'transport_price',
            'attributes' => [
                'min'         => 0,
                'step'        => 0.01,
                'placeholder' => 'Enter price of additional transportation costs',
            ],
            'options'    => [
                'label' => 'Additional transportation costs',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'transport_comment',
            'attributes' => [
                'rows'        => 3,
                'placeholder' => 'Enter a comment for additional transportation costs',
            ],
            'options'    => [
                'label' => 'Comment for additional transportation costs',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_remain',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and remain',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_exit',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and exit',
            ],
        ]);

    }

}