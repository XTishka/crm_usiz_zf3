<?php

namespace Document\Form;

use Zend\Form\Form;

class PurchaseUnloadingWagon extends Form {

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
            'name' => 'wagon_id',
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'contract_id',
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'unloading_weight',
            'attributes' => [
                'step'        => 0.001,
                'placeholder' => 'Enter unloading weight',
                'value'       => 50,
            ],
            'options'    => [
                'label' => 'Weight of unloading',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'unloading_date',
            'attributes' => [
                'value'            => date('d.m.Y'),
                'data-date-format' => 'dd.mm.yyyy',
            ],
            'options'    => [
                'label' => 'Date of unloading',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'unload',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Unload wagon',
            ],
        ]);

    }

}