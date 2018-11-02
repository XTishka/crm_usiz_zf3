<?php

namespace Document\Form;


use Transport\Service\Repository\CarrierDb;
use Transport\Service\Repository\RateDb;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class SaleImportWagon extends Form implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type' => 'hidden',
            'name' => 'contract_id',
        ]);

        $this->add([
            'type'    => 'file',
            'name'    => 'file',
            'options' => [
                'label' => 'Select file to upload',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'carrier_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'rate_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select transport rate',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'import',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Import',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'carrier_id' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please select carrier.',
                        ],
                    ]],
                    ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                        'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                        'table'    => CarrierDb::TABLE_CARRIERS,
                        'field'    => 'carrier_id',
                        'messages' => [
                            'noRecordFound' => 'The selected carrier was not found in the database.',
                        ],
                    ]],
                ],
            ],
            'rate_id' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please select transport rate.',
                        ],
                    ]],
                    ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                        'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                        'table'    => RateDb::TABLE_RATES,
                        'field'    => 'rate_id',
                        'messages' => [
                            'noRecordFound' => 'The selected transport rate was not found in the database.',
                        ],
                    ]],
                ],
            ],
        ];
    }


}