<?php

namespace Document\Form;

use Transport\Form\Element\CarrierSelect;
use Zend\Form\Form;

class AddCarrierBalance extends Form {


    public function init() {

        $this->add([
            'type'    => CarrierSelect::class,
            'name'    => 'carrier_id',
            'options' => [
                'label' => 'Select carrier',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'amount',
            'attributes' => [
                'min' => 0,
            ],
            'options'    => [
                'label' => 'Enter amount',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'apply',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Apply',
            ],
        ]);

    }

}