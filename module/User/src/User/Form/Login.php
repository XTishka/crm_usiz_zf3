<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Login extends Form implements InputFilterProviderInterface {

    public function __construct($name = 'user_login') {
        parent::__construct($name);
    }

    public function init() {

        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                ],
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'email',
            'attributes' => [
                'type' => 'email',
            ],
            'options'    => [
                'label' => 'Email Address',
            ],
        ]);

        $this->add([
            'type'    => 'password',
            'name'    => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'redirect_url',
        ]);

        $this->add([
            'type'    => 'checkbox',
            'name'    => 'remember_me',
            'options' => [
                'checkedValue'   => 1,
                'uncheckedValue' => 0,
                'label'          => 'Remember me on this computer',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'login',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Login',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'email'    => [
                'required' => true,
            ],
            'password' => [
                'required' => true,
            ],
        ];
    }


}