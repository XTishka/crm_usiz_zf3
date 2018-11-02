<?php

namespace Manufacturing\Form;

use Contractor\Form\Element\ContractorPlantSelect;
use Zend\Form\Form;

class Warehouse extends Form {

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
            'name' => 'warehouse_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'warehouse_name',
            'attributes' => [
                'placeholder' => 'Enter the name of warehouse',
            ],
            'options'    => [
                'label' => 'Name of warehouse',
            ],
        ]);

        $this->add([
            'type'    => Element\WarehouseTypeSelect::class,
            'name'    => 'warehouse_type',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select warehouse type',
            ],
        ]);

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'plant_id',
            'options' => [
                'empty_option' => 'Please choose serviced plant',
                'label'        => 'Serviced plant',
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'description',
            'attributes' => [
                'placeholder' => 'Enter description of warehouse',
            ],
            'options'    => [
                'label' => 'Description of warehouse',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'capacity',
            'attributes' => [
                'placeholder' => 'Enter capacity of warehouse',
                'min'         => 0,
                'step'        => 1,
            ],
            'options'    => [
                'label' => 'Capacity of warehouse',
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