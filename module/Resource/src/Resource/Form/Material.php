<?php

namespace Resource\Form;

use Zend\Form\Form;

class Material extends Form {

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
            'name' => 'material_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'material_name',
            'attributes' => [
                'placeholder' => 'Please enter the name of the material',
                'maxlength'   => 255,
            ],
            'options'    => [
                'label' => 'Name of the material',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Please enter the description of the material',
                'rows'        => 4,
            ],
            'options'    => [
                'label' => 'Description of the material',
            ],
        ]);

        $this->add([
            'type'    => Fieldset\Fraction::class,
            'name'    => 'fraction',
            'options' => [
                'label'                => 'Enter the material fraction',
                'use_as_base_fieldset' => false,
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