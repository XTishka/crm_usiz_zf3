<?php

namespace Application\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Email extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'       => 'text',
            'name'       => 'email',
            'attributes' => [
                'placeholder' => 'Enter email address',
            ],
            'options'    => [
                'label' => 'Email address',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter description of email address',
            ],
            'options'    => [
                'label' => 'Description of email address',
            ],
        ]);

        $this->add([
            'type'       => 'button',
            'name'       => 'remove',
            'attributes' => [
                'class' => 'btn btn-danger',
            ],
            'options'    => [
                'label' => 'Remove email',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'email'       => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the email address.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The email address is too long, the maximum length is %max% characters.',
                        ],
                    ]],
                    ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                        'callback' => function ($value) {
                            return filter_var($value, FILTER_VALIDATE_EMAIL);
                        },
                        'messages' => [
                            'callbackValue' => 'Please enter valid email address.',
                        ],
                    ]],
                ],
            ],
            'description' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the description of the email address.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The description is too long, the maximum length is %max% characters.',
                        ],
                    ]],
                ],
            ],
        ];
    }

}