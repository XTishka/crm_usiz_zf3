<?php

namespace Contractor\Form;

use Zend\Form\Form;

class ContractorCommonFilter extends Form
{

    public function init()
    {

        $this->setAttribute('method', 'get');
        $this->setAttribute('id', 'contractor-filter');

        $this->add([
            'name'       => 'contractor_type',
            'type'       => 'select',
            'attributes' => [
                'form' => 'contractor-filter',
            ],
            'options'    => [
                'label'         => 'Contractor type',
                'empty_option'  => 'Select contractor type',
                'value_options' => [
                    'consignee'  => 'Consignee contractors',
                    'plant'      => 'Plant contractors',
                    'corporate'  => 'Сorporate contractors',
                    'customer'   => 'Сustomer contractors',
                    'provider'   => 'Providers',
                    'company'    => 'Company contractors',
                    'additional' => 'Additionals',
                ],
            ],
        ]);

        $this->add([
            'name'       => 'contractor_name',
            'type'       => 'text',
            'attributes' => [
                'form' => 'contractor-filter',
            ],
            'options'    => [
                'label' => 'Contractor name',
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