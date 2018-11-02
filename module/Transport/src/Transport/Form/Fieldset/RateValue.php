<?php

namespace Transport\Form\Fieldset;

use Transport\Domain\RateValueEntity;
use Zend\Form\Fieldset;
use Zend\Hydrator\ClassMethods;
use Zend\InputFilter\InputFilterProviderInterface;

class RateValue extends Fieldset implements InputFilterProviderInterface {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $this->setObject(new RateValueEntity());
        $this->setHydrator(new ClassMethods());
    }

    public function init() {

        $this->add([
            'type' => 'hidden',
            'name' => 'value_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'weight',
            'attributes' => [
                'step' => 0.001,
            ],
            'options'    => [
                'label' => 'Enter the weight of rate',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'price',
            'attributes' => [
                'min'  => 0,
                'step' => 0.01,
            ],
            'options'    => [
                'label' => 'Enter the price of rate',
            ],
        ]);

        $this->add([
            'type'       => 'button',
            'name'       => 'remove',
            'attributes' => [
                'class' => 'btn btn-max btn-danger',
            ],
            'options'    => [
                'label' => 'Remove row',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'value_id'   => [
                'filters' => [
                    ['name' => 'ToInt'],
                ],
            ],
            'weight'     => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please select the type of rate.',
                        ],
                    ]],
                    /*
                    ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                        'callback' => function ($value, $context) {
                            $pattern = $context['rate_type'] == RateEntity::TYPE_FIXED_STATIC_WEIGHT ? '/^\d+(\.\d*)?$/' : '/^\d+(\.\d*)?-\d+(\.\d*)?$/';
                            return preg_match($pattern, $value);
                        },
                        'messages' => [
                            'callbackValue' => 'Invalid weight value specified',
                        ],
                    ]],
                    */
                ],
            ],
            'price'      => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'NumberParse'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a price of rate.',
                        ],
                    ]],
                ],
            ],
        ];
    }

}