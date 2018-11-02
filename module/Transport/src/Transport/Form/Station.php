<?php

namespace Transport\Form;

use Application\Form\Element\CountrySelect;
use Zend\Form\Form;

class Station extends Form {

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
            'name' => 'station_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'station_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the station',
                'maxlength'   => 255,
            ],
            'options'    => [
                'label' => 'Name of the station',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'station_code',
            'attributes' => [
                'placeholder' => 'Please enter the code of the station',
                'maxlength'   => 32,
            ],
            'options'    => [
                'label' => 'Code of the station',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Please enter the description of the station',
                'rows'        => 4,
            ],
            'options'    => [
                'label' => 'Description of the station',
            ],
        ]);

        $this->add([
            'type'    => CountrySelect::class,
            'name'    => 'country',
            'options' => [
                'label' => 'Country of the carrier',
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