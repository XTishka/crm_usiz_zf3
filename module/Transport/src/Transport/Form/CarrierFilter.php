<?php

namespace Transport\Form;

use Zend\Form\Form;

class CarrierFilter extends Form {

    public function init() {

        $this->setAttribute('method', 'get');
        $this->setAttribute('id', 'carrier-filter');

        $this->add([
            'name'       => 'carrier_name',
            'type'       => 'text',
            'attributes' => [
                'form' => 'carrier-filter',
            ],
            'options'    => [
                'label' => 'Carrier name',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'register_code',
            'attributes' => [
                'placeholder' => 'Enter register code of contractor',
            ],
            'options'    => [
                'label' => 'Register code of contractor',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'filter',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Apply filter',
            ],
        ]);

    }

}