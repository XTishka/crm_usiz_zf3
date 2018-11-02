<?php

namespace Contractor\Form;

use Application\Form\Fieldset;
use Zend\Form\Form;

abstract class ContractorAbstract extends Form {

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
            'name' => 'contractor_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'contractor_name',
            'attributes' => [
                'placeholder' => 'Enter the name of contractor',
            ],
            'options'    => [
                'label' => 'Name of contractor',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'full_name',
            'attributes' => [
                'placeholder' => 'Enter the full name of contractor',
            ],
            'options'    => [
                'label' => 'Full name of contractor',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter description of contractor',
            ],
            'options'    => [
                'label' => 'Description of contractor',
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
            'type'       => 'text',
            'name'       => 'bank_account',
            'attributes' => [
                'placeholder' => 'Enter bank account of contractor',
            ],
            'options'    => [
                'label' => 'Bank account of contractor',
            ],
        ]);

        $this->add([
            'type'    => Fieldset\Address::class,
            'name'    => 'address',
            'options' => [
                'label'                => 'Enter the postal address of contractor',
                'use_as_base_fieldset' => false,
            ],
        ]);

        $this->add([
            'type'    => Fieldset\Person::class,
            'name'    => 'person',
            'options' => [
                'label'                => 'Contact person',
                'use_as_base_fieldset' => false,
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'emails',
            'options' => [
                'label'                  => 'Email addresses',
                'count'                  => 0,
                'use_as_base_fieldset'   => false,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Fieldset\Email::class,
                ],
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'phones',
            'options' => [
                'label'                  => 'Phone numbers',
                'count'                  => 0,
                'use_as_base_fieldset'   => false,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Fieldset\Phone::class,
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