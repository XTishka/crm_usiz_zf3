<?php

namespace Document\Form\Fieldset;

use Contractor\Form\Element\ContractorAdditionalSelect;
use Zend\Form\Element\Select;
use Zend\Form\Fieldset;
use Zend\Hydrator\ArraySerializable;
use Zend\InputFilter\InputFilterProviderInterface;

class Wagon extends Fieldset implements InputFilterProviderInterface {

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
            'type'    => 'select',
            'name'    => 'rate_value_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Tariff grid',
            ],
        ]);

        $this->add([
            'type'    => ContractorAdditionalSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'empty_option' => 'Select contractor',
                'label'        => 'Contractor',
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
            'type'       => 'text',
            'name'       => 'transport_comment',
            'attributes' => [
                'placeholder' => 'Enter a comment for additional transportation costs',
            ],
            'options'    => [
                'label' => 'Comment for additional transportation costs',
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
            'rate_id'           => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        //'type'     => 'integer',
                        'messages' => [
                            'isEmpty' => 'Select the rate.',
                        ],
                    ]],
                ],
            ],
            'wagon_number'      => [
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
            'loading_weight'    => [
                'filters'    => [
                    //['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter the loading weight of wagon.',
                        ],
                    ]],
                ],
            ],
            'rate_value_id'     => [
                'required' => false,
            ],
            'contractor_id'     => [
                'required' => false,
            ],
            'transport_price'   => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter price of additional transportation costs.',
                        ],
                    ]],
                ],
            ],
            'transport_comment' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Enter a comment for additional transportation costs.',
                        ],
                    ]],
                ],
            ],
        ];
    }

}