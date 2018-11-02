<?php

namespace Resource\Form;

use Zend\Form\Form;

class Product extends Form {

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
            'name' => 'product_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'product_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the product',
                'maxlength'   => 255,
            ],
            'options'    => [
                'label' => 'Name of the product',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Please enter the description of the product',
                'rows'        => 4,
            ],
            'options'    => [
                'label' => 'Description of the product',
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