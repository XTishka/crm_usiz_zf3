<?php

namespace Application\Form;

use Zend\Form\Form;

class Country extends Form {

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
            'name' => 'country_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'country_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the country',
                'maxlength'   => 255,
            ],
            'options'    => [
                'label' => 'Name of the country',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'country_code',
            'attributes' => [
                'placeholder' => 'Please enter the code of the country',
                'maxlength'   => 2,
            ],
            'options'    => [
                'label' => 'Code of the country',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'locale',
            'attributes' => [
                'placeholder' => 'Please enter the locale in the «uk_UA» format',
                'maxlength'   => 5,
            ],
            'options'    => [
                'label' => 'Locale of the country',
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