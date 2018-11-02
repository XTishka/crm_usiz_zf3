<?php

namespace Manufacturing\Form\Fieldset;

use Contractor\Entity\ContractorAbstract;
use Contractor\Form\Element\ContractorProviderSelect;
use Contractor\Form_old\Element\ProviderSelect;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Contractor\Service_old\Repository\ProviderDb;
use Manufacturing\Form\Element\WarehouseBalancesSelect;
use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class SkipMaterial extends Fieldset implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'    => ContractorProviderSelect::class,
            'name'    => 'provider_id',
            'options' => [
                'empty_option' => 'Please select provider',
                'label'        => 'Select provider',
            ],
        ]);

        $this->add([
            'type'    => WarehouseBalancesSelect::class,
            'name'    => 'material_id',
            'options' => [
                'empty_option' => 'Please select material',
                'label'        => 'Select material',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'weight',
            'attributes' => [
                'min'  => 0,
                'step' => 0.001,
            ],
            'options'    => [
                'label' => 'Weight',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'dropout',
            'attributes' => [
                'min'   => 0,
                'step'  => 0.001,
                'value' => 10,
            ],
            'options'    => [
                'label' => 'Dropout',
            ],
        ]);

        $this->add([
            'type'       => 'button',
            'name'       => 'remove',
            'attributes' => [
                'class' => 'btn btn-max btn-error',
            ],
            'options'    => [
                'label' => 'Remove',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'provider_id' => [
                'filters'    => [
                    ['name' => 'ToInt'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please select provider.',
                        ],
                    ]],
                    ['name' => 'DbRecordExists', 'break_chain_on_failure' => true, 'options' => [
                        'adapter'  => GlobalAdapterFeature::getStaticAdapter(),
                        'table'    => DatabaseContractorAbstract::TABLE_CONTRACTORS,
                        'field'    => 'contractor_id',
                        'exclude'  => sprintf('contractor_type = %s', GlobalAdapterFeature::getStaticAdapter()->platform->quoteValue(ContractorAbstract::TYPE_PROVIDER)),
                        'messages' => [
                            'noRecordFound' => 'The selected provider was not found in the database.',
                        ],
                    ]],
                ],
            ],
            'weight'      => [
                'filters'    => [
                    ['name' => 'NumberParse', 'options' => ['locale' => 'en_US']],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the loading weight.',
                        ],
                    ]],
                    ['name' => 'GreaterThan', 'break_chain_on_failure' => true, 'options' => [
                        'inclusive' => true,
                        'min'       => 0,
                        'messages'  => [
                            'notGreaterThan' => 'The min weight must be greater than or equal to %min%.',
                        ],
                    ]],
                ],
            ],
            'dropout'     => [
                'filters'    => [
                    ['name' => 'NumberFormat', 'options' => [
                        'locale' => 'en_US',
                        'style'  => \NumberFormatter::DECIMAL,
                        'type'   => \NumberFormatter::TYPE_DOUBLE,
                    ]],
                ],
                'validators' => [
                    ['name' => 'NotEmpty', 'break_chain_on_failure' => true, 'options' => [
                        'messages' => [
                            'isEmpty' => 'Please enter the dropout rate',
                        ],
                    ]],
                    ['name' => 'GreaterThan', 'break_chain_on_failure' => true, 'options' => [
                        'inclusive' => true,
                        'min'       => 0,
                        'messages'  => [
                            'notGreaterThan' => 'The min dropout rate must be greater than or equal to %min%',
                        ],
                    ]],
                ],
            ],
        ];
    }

}