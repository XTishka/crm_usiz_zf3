<?php

namespace Application\Form\Fieldset;

use Application\Form\Element\CountrySelect;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\StaticValidator;

class Address extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'       => 'text',
            'name'       => 'street_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the street',
            ],
            'options'    => [
                'label' => 'Name of the street',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'street_number',
            'attributes' => [
                'placeholder' => 'Please enter the house number',
            ],
            'options'    => [
                'label' => 'House number',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'city',
            'attributes' => [
                'placeholder' => 'Please enter a city name',
            ],
            'options'    => [
                'label' => 'City name',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'state',
            'attributes' => [
                'placeholder' => 'Please enter a name for the state',
            ],
            'options'    => [
                'label' => 'Name for the state',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'post_code',
            'attributes' => [
                'placeholder' => 'Please enter the post code',
            ],
            'options'    => [
                'label' => 'Post code',
            ],
        ]);

        $this->add([
            'type'    => CountrySelect::class,
            'name'    => 'country',
            'options' => [
                'label' => 'Select country',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'street_name'   => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the name of the street.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The street name is too long, the maximum length is %max% characters.',
                        ],
                    ]],
                ],
            ],
            'street_number' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the house number.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The street number is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'city'          => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a city name.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The city is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'state'         => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a state name.',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooLong' => 'The state is too long, the maximum length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'post_code'     => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the post code.',
                        ],
                    ]],
                    ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                        'callback' => function ($value, $context) {
                            $options = ['locale' => sprintf('%s_%s', strtolower($context['country']), $context['country'])];
                            return StaticValidator::execute($value, 'PostCode', $options);
                        },
                        'messages' => [
                            'callbackValue' => 'Invalid post code',
                        ],
                    ]],
                ],
            ],
            'country'       => [
                'required' => false,
            ],
        ];
    }

}