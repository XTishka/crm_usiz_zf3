<?php

namespace Transport\Form;

use Application\Form\Element\CountrySelect;
use Application\Form\Fieldset\Email;
use Application\Form\Fieldset\Person;
use Application\Form\Fieldset\Phone;
use Zend\Form\Form;

class Carrier extends Form {

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
            'name' => 'carrier_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'carrier_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the carrier',
                'maxlength'   => 255,
            ],
            'options'    => [
                'label' => 'Name of the carrier',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Please enter the description of the carrier',
                'rows'        => 4,
            ],
            'options'    => [
                'label' => 'Description of the carrier',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'register_code',
            'attributes' => [
                'placeholder' => 'Enter register code of contractor',
            ],
            'options'    => [
                'label' => 'Register code of contractor',
            ],
        ]);

        $this->add([
            'type'    => Element\CarrierTypeSelect::class,
            'name'    => 'carrier_type',
            'options' => [
                'label' => 'Type of the carrier',
            ],
        ]);

        $this->add([
            'type'    => CountrySelect::class,
            'name'    => 'country',
            'options' => [
                'label' => 'Country of the carrier',
            ],
        ]);

        $this->add([
            'type'    => Person::class,
            'name'    => 'person',
            'options' => [
                'label' => 'Enter the name of the contact person',
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'emails',
            'options' => [
                'label'                  => 'Enter the email address of the carrier',
                'count'                  => 0,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Email::class,
                ],
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'phones',
            'options' => [
                'label'                  => 'Enter the phone number of the carrier',
                'count'                  => 0,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Phone::class,
                ],

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