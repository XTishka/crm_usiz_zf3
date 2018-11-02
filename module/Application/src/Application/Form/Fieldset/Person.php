<?php

namespace Application\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Person extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'       => 'text',
            'name'       => 'first_name',
            'attributes' => [
                'placeholder' => 'Enter first name',
            ],
            'options'    => [
                'label' => 'First name',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'middle_name',
            'attributes' => [
                'placeholder' => 'Enter middle name',
            ],
            'options'    => [
                'label' => 'Middle name',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'last_name',
            'attributes' => [
                'placeholder' => 'Enter last name',
            ],
            'options'    => [
                'label' => 'Last name',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'first_name'  => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'The first name is required',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 64,
                        'messages' => [
                            'stringLengthTooLong' => 'The first name is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'middle_name' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'The middle name is required',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 64,
                        'messages' => [
                            'stringLengthTooLong' => 'The middle name is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'last_name'   => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'The last name is required',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 64,
                        'messages' => [
                            'stringLengthTooLong' => 'The last name is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
        ];
    }

}