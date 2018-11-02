<?php

namespace Document\Form;

use Contractor\Form_old\Element\CustomerSelect;
use Zend\Form\Form;

class AddCustomerBalance extends Form {


    public function init() {

        $this->add([
            'type'    => CustomerSelect::class,
            'name'    => 'customer_id',
            'options' => [
                'label' => 'Select customer',
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