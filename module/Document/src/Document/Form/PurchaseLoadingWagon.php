<?php

namespace Document\Form;

use Zend\Form\Form;

class PurchaseLoadingWagon extends Form {

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
            'type'    => 'collection',
            'name'    => 'wagons',
            'options' => [
                'label'                  => 'Please add wagons for this contract',
                'count'                  => 1,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Fieldset\Wagon::class,
                ],
            ],
        ]);

        $this->add([
            'type'       => 'select',
            'name'       => 'carrier_id',
            'options'    => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier',
            ],
        ]);

        $this->add([
            'type'       => 'select',
            'name'       => 'rate_id',
            'options'    => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select transport rate',
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