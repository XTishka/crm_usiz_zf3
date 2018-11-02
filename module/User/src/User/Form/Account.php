<?php


namespace User\Form;


use Zend\Form\Form;

class Account extends Form {

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
            'type' => 'hidden',
            'name' => 'user_id',
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'first_name',
            'options' => [
                'label' => 'First name',
            ],
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'last_name',
            'options' => [
                'label' => 'Last name',
            ],
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'email',
            'options' => [
                'label' => 'Email address',
            ],
        ]);

        $this->add([
            'type'    => 'text',
            'name'    => 'phone',
            'options' => [
                'label' => 'Phone number',
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
            'type'    => 'password',
            'name'    => 'confirm_password',
            'options' => [
                'label' => 'Confirm password',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'role',
            'options' => [
                'label'         => 'Select user role',
                'value_options' => [
                    'admin'   => 'Administrator',
                    'manager' => 'Manager',
                    'guest'   => 'Guest',
                ],
            ],
        ]);

        $this->add([
            'type'    => 'checkbox',
            'name'    => 'is_active',
            'options' => [
                'checkedValue'   => '1',
                'uncheckedValue' => '0',
                'label'          => 'Check to activate this user',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_remain',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and remain',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_exit',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and exit',
            ],
        ]);

    }

}