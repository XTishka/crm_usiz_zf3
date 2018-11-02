<?php

namespace Resource\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Fraction extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'       => 'number',
            'name'       => 'min_size',
            'attributes' => [
                'min'  => 0,
                'step' => 1,
            ],
            'options'    => [
                'label' => 'Minimum size',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'max_size',
            'attributes' => [
                'min'  => 0,
                'step' => 1,
            ],
            'options'    => [
                'label' => 'Maximum size',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'min_size' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'isEmpty' => 'Please enter the minimum size.',
                    ]],
                    ['name' => 'GreaterThan', 'break_chain_on_failure' => true, 'options' => [
                        'inclusive' => true,
                        'min'       => 0,
                        'messages'  => [
                            'notGreaterThan' => 'The minimum size must be greater than or equal to %min%',
                        ],
                    ]],
                ],
            ],
            'max_size' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'isEmpty' => 'Please enter the maximum size.',
                    ]],
                    ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                        'callback' => function ($value, $context) {
                            return $context['min_size'] <= $value;
                        },
                        'messages' => [
                            'callbackValue' => 'The maximum size must be greater than or equal to min size.',
                        ],
                    ]],
                ],
            ],
        ];
    }

}