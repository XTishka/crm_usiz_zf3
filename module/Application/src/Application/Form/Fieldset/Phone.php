<?php

namespace Application\Form\Fieldset;

use Application\Form\Element\CountrySelect;
use Zend\Filter\StaticFilter;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StaticValidator;

class Phone extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'    => CountrySelect::class,
            'name'    => 'country',
            'options' => [
                'label' => 'Select a country',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'number',
            'attributes' => [
                'placeholder' => 'Enter phone number',
            ],
            'options'    => [
                'label' => 'Phone number',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter description of phone number',
            ],
            'options'    => [
                'label' => 'Description of phone number',
            ],
        ]);

        $this->add([
            'type'       => 'button',
            'name'       => 'remove',
            'attributes' => [
                'class' => 'btn btn-danger',
            ],
            'options'    => [
                'label' => 'Remove phone',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'number'      => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'Callback', 'options' => [
                        'callback' => function ($value) {
                            return sprintf('+%s', StaticFilter::execute($value, 'Digits'));
                        },
                    ]],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the phone number.',
                        ],
                    ]],
                    ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                        'callback' => function ($value, $context) {
                            $country = $context['country'] ?? 'UA';
                            return StaticValidator::execute($value, 'PhoneNumber', ['country' => $country]);
                        },
                        'messages' => [
                            'callbackValue' => 'Please enter valid phone number.',
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
                            'isEmpty' => 'Please enter the description of the phone number.',
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