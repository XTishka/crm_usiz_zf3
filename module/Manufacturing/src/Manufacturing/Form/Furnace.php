<?php

namespace Manufacturing\Form;

use Contractor\Form\Element\ContractorPlantSelect;
use Zend\Form\Form;

class Furnace extends Form {

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
            'name' => 'furnace_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'furnace_name',
            'attributes' => [
                'placeholder' => 'Enter the name of the furnace',
            ],
            'options'    => [
                'label' => 'Name of the furnace',
            ],
        ]);

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'plant_id',
            'options' => [
                'label' => 'Select plant',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter the description of the furnace',
            ],
            'options'    => [
                'label' => 'Description of the furnace',
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