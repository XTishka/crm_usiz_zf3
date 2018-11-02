<?php

namespace Document\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\Hydrator\ArraySerializable;
use Zend\InputFilter\InputFilterProviderInterface;

class SaleWagon extends Fieldset implements InputFilterProviderInterface {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $this->setObject(new \ArrayObject());
        $this->setHydrator(new ArraySerializable());
    }

    public function init() {

        $this->add([
            'type' => 'hidden',
            'name' => 'rate_id',
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
            'type'       => 'button',
            'name'       => 'remove',
            'attributes' => [
                'class' => 'btn btn-max btn-danger',
            ],
            'options'    => [
                'label' => 'Remove wagon',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'rate_id'        => [
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'type'     => 'integer',
                        'messages' => [
                            'isEmpty' => 'Select the rate.',
                        ],
                    ]],
                ],
            ],
            'wagon_number'   => [
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter the number of wagon.',
                        ],
                    ]],
                ],
            ],
            'loading_weight' => [
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter the loading weight of wagon.',
                        ],
                    ]],
                ],
            ],
        ];
    }

}