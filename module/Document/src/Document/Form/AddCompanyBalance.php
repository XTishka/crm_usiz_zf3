<?php

namespace Document\Form;

use Manufacturing\Form\Element\CompanySelect;
use Zend\Form\Form;

class AddCompanyBalance extends Form {


    public function init() {

        $this->add([
            'type'    => CompanySelect::class,
            'name'    => 'company_id',
            'options' => [
                'label' => 'Select company',
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