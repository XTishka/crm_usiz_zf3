<?php


namespace User\InputFilter;


use User\Service\Repository\DatabaseAccount;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db;

class Account extends InputFilter {

    public function init() {

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true],
                ['name' => 'Csrf', 'break_chain_on_failure' => true],
            ],
        ], 'csrf');

        $this->add([
            'filters' => [
                ['name' => 'ToInt'],
            ],
        ], 'user_id');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the first name of user.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 64,
                    'messages' => [
                        'stringLengthTooLong' => 'First name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'first_name');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the last name of user.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 64,
                    'messages' => [
                        'stringLengthTooLong' => 'Last name is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'last_name');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the email address of user.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooLong' => 'Email address is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value) {
                        return filter_var($value, FILTER_VALIDATE_EMAIL);
                    },
                    'messages' => [
                        'callbackValue' => 'Please enter a valid email address.',
                    ],
                ]],
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, $context) {
                        $validator = new Db\NoRecordExists([
                            'adapter' => GlobalAdapterFeature::getStaticAdapter(),
                            'table'   => DatabaseAccount::TABLE_USER_ACCOUNTS,
                            'field'   => 'email',
                            'exclude' => [
                                'field' => 'user_id',
                                'value' => $context['user_id'],
                            ],
                        ]);
                        return $validator->isValid($value);
                    },
                    'messages' => [
                        'callbackValue' => 'This email address is already registered.',
                    ],
                ]],
            ],
        ], 'email');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the phone number of user.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'max'      => 16,
                    'messages' => [
                        'stringLengthTooLong' => 'Phone number is too long. The maximum length is %max% characters.',
                    ],
                ]],
                ['name' => 'PhoneNumber', 'break_chain_on_failure' => true, 'options' => [
                    'country'  => 'ua',
                    'messages' => [
                        'phoneNumberNoMatch'     => 'Please enter a valid phone number.',
                        'phoneNumberUnsupported' => 'The country provided is currently unsupported.',
                    ],
                ]],
                ['name' => 'Callback', 'break_chain_on_failure' => true, 'options' => [
                    'callback' => function ($value, array $context) {
                        $validator = new Db\NoRecordExists([
                            'adapter' => GlobalAdapterFeature::getStaticAdapter(),
                            'table'   => DatabaseAccount::TABLE_USER_ACCOUNTS,
                            'field'   => 'phone',
                            'exclude' => [
                                'field' => 'user_id',
                                'value' => $context['user_id'],
                            ],
                        ]);
                        return $validator->isValid($value);
                    },
                    'messages' => [
                        'callbackValue' => 'This phone number is already registered.',
                    ],
                ]],
            ],
        ], 'phone');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the password.',
                    ],
                ]],
                ['name' => 'StringLength', 'break_chain_on_failure' => true, 'options' => [
                    'min'      => 6,
                    'max'      => 255,
                    'messages' => [
                        'stringLengthTooShort' => 'Password is too short. The maximum length is %min% characters.',
                        'stringLengthTooLong'  => 'Password is too long. The maximum length is %max% characters.',
                    ],
                ]],
            ],
        ], 'password');

        $this->add([
            'required'   => true,
            'filters'    => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                    'messages' => [
                        'isEmpty' => 'Please enter the password confirm.',
                    ],
                ]],
                ['name' => 'Identical', 'break_chain_on_failure' => true, 'options' => [
                    'token'    => 'password',
                    'messages' => [
                        'notSame' => 'The passwords you entered do not match.',
                    ],
                ]],
            ],
        ], 'confirm_password');

    }

}