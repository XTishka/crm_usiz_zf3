<?php

namespace Application\Form;

use Zend\Form\Form;

class Tax extends Form {

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
            'name' => 'tax_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'tax_name',
            'attributes' => [
                'placeholder' => 'Enter name of the tax',
            ],
            'options'    => [
                'label' => 'Name of the tax',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter description of the tax',
            ],
            'options'    => [
                'label' => 'Description of the tax',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'value',
            'attributes' => [
                'placeholder' => 'Enter value of the tax',
                'min'         => 0,
                'step'        => 0.01,
            ],
            'options'    => [
                'label' => 'Value of the tax',
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