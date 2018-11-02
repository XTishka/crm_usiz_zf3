<?php

namespace Bank\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class BankForm extends Form implements InputFilterProviderInterface {

    public function init() {

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
            'name' => 'bank_id',
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'name',
            'options' => [
                'label' => 'Bank name',
            ],
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'code',
            'options' => [
                'label' => 'Bank code',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save changes',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'csrf'    => [
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
            'bank_id' => [
                'filters' => [
                    ['name' => 'ToInt'],
                ],
            ],
            'name'    => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Bank name is empty',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'min'      => 2,
                        'max'      => 255,
                        'messages' => [
                            'stringLengthTooShort' => 'The bank name entered is too short. The minimum allowed length is %min% characters',
                            'stringLengthTooLong'  => 'The bank name entered is too long. The maximum allowed length is %max% characters',
                        ],
                    ]],
                ],
            ],
            'code'    => [
                'required'   => true,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Bank name is empty',
                        ],
                    ]],
                    ['name' => 'Digits', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'notDigits' => 'Bank code must contain only digits',
                        ],
                    ]],
                    ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                        'min'      => 6,
                        'max'      => 6,
                        'messages' => [
                            'stringLengthTooShort' => 'The bank code entered is too short. The minimum allowed length is %min% characters',
                            'stringLengthTooLong'  => 'The bank code entered is too long. The maximum allowed length is %max% characters',
                        ],
                    ]],
                ],
            ],
        ];
    }


}