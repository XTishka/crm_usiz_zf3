<?php

namespace Reports\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorConsigneeSelect;
use Contractor\Form\Element\ContractorCustomerSelect;
use Resource\Form\Element\ProductSelect;
use Transport\Form\Element\CarrierSelect;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ShipmentsFilterForm extends Form implements InputFilterProviderInterface
{

    /**
     * @throws \Exception
     */
    public function init()
    {

        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'formShipmentsFilter');

        $periodBegin = new \DateTime();
        $periodEnd = clone $periodBegin;
        $periodEnd->add(new \DateInterval('P10D'));

        $this->add([
            'type'    => ContractorCompanySelect::class,
            'name'    => 'company_id',
            'options' => [
                'label' => 'Select company',
            ],
        ]);

        $this->add([
            'type'    => ContractorCustomerSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select contractor',
            ],
        ]);

        $this->add([
            'type'    => ContractorConsigneeSelect::class,
            'name'    => 'consignee_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select consignee',
            ],
        ]);

        $this->add([
            'type'    => CarrierSelect::class,
            'name'    => 'carrier_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier',
            ]
        ]);

        $this->add([
            'type'    => ProductSelect::class,
            'name'    => 'product_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select product',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_begin',
            'attributes' => [
                'value' => $periodBegin->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period begin',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_end',
            'attributes' => [
                'value' => $periodEnd->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period end',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-info',
                'value' => 'Download report',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Download report',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'html',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Show report',
            ],
        ]);

    }

    public function getInputFilterSpecification()
    {
        return [
            'contractor_id' => [
                'required' => false,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
            ],
            'consignee_id'  => [
                'required' => false,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
            ],
            'carrier_id'  => [
                'required' => false,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
            ],
            'product_id'    => [
                'required' => false,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
            ],
            'period_begin'  => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format' => 'd.m.Y',
                    ]],
                ],
            ],
            'period_end'    => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format' => 'd.m.Y',
                    ]],
                ],
            ],
        ];
    }


}