<?php

namespace Resource\Form;

use Contractor\Form\Element\ContractorProviderSelect;
use Resource\Form\Element\MaterialSelect;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class DropoutForm extends Form implements InputFilterProviderInterface {

    /**
     * @throws \Exception
     */
    public function init() {

        $periodBegin = new \DateTime();
        $periodEnd = clone $periodBegin;
        $periodEnd->add(new \DateInterval('P1Y'));

        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                ],
            ],
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'dropout_id',
        ]);

        $this->add([
            'type'    => ContractorProviderSelect::class,
            'name'    => 'provider_id',
            'options' => [
                'label' => 'Select provider',
            ],
        ]);

        $this->add([
            'type'    => MaterialSelect::class,
            'name'    => 'material_id',
            'options' => [
                'label' => 'Select material',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'value',
            'attributes' => [
                'min'   => 0,
                'step'  => 0.0001,
                'value' => 15.00,
            ],
            'options'    => [
                'label' => 'Dropout rate value',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_begin',
            'attributes' => [
                'value' => $periodBegin->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period begin',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_end',
            'attributes' => [
                'value' => $periodEnd->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period end',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Save changes',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'csrf'         => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'CSRF is empty',
                        ],
                    ]],
                    ['name' => 'Csrf', 'break_chain_on_failure' => true],
                ],
            ],
            'dropout_id'   => [
                'filters' => [
                    ['name' => 'ToInt'],
                ],
            ],
            'value'        => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'callback', 'options' => [
                        'callback' => 'floatval'
                    ]],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a value of dropout.',
                        ],
                    ]],
                ],
            ],
            'period_begin' => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a begin period of dropout.',
                        ],
                    ]],
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format'   => 'd.m.Y',
                        'messages' => [
                            'dateInvalidDate' => 'Enter a valid start date for the period',
                            'dateFalseFormat' => 'Enter the start date of the period in the format %format%',
                        ],
                    ]],
                ],
            ],
            'period_end'   => [
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter a begin period of dropout.',
                        ],
                    ]],
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format'   => 'd.m.Y',
                        'messages' => [
                            'dateInvalidDate' => 'Enter a valid end date for the period',
                            'dateFalseFormat' => 'Enter the end date of the period in the format %format%',
                        ],
                    ]],
                ],
            ],
        ];
    }


}