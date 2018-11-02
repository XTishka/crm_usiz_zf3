<?php

namespace Document\Form;

use Contractor\Form_old\Element\ProviderSelect;
use Zend\Form\Form;

class AddProviderBalance extends Form {


    public function init() {

        $this->add([
            'type'    => ProviderSelect::class,
            'name'    => 'provider_id',
            'options' => [
                'label' => 'Select provider',
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