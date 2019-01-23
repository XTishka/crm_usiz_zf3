<?php

namespace Reports\Form;

use Contractor\Form\Element\ContractorAllSelect;
use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorTypeSelect;
use Document\Domain\TransactionEntity;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class FinanceFilterForm extends Form implements InputFilterProviderInterface {

    /**
     * @throws \Exception
     */
    public function init() {

        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'formFinanceFilter');

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
            'type'    => ContractorTypeSelect::class,
            'name'    => 'contractor_type',
            'options' => [
                'empty_option'              => 'Please choose contractor type',
                'disable_inarray_validator' => true,
                'label'                     => 'Select contractor type',
            ],
        ]);

        $this->add([
            'type'    => ContractorAllSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'empty_option'              => 'Please choose contractor',
                'disable_inarray_validator' => true,
                'label'                     => 'Select contractor',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'transaction_route',
            'options' => [
                'empty_option'              => 'Please choose debit or credit',
                'disable_inarray_validator' => true,
                'label'                     => 'Select debit or credit',
                'value_options'             => [
                    'credit' => 'Credit',
                    'debit'  => 'Debit',
                ],
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'transaction_type',
            'options' => [
                'empty_option'              => 'Please choose transaction type',
                'disable_inarray_validator' => true,
                'label'                     => 'Select transaction type',
                'value_options'             => [
                    TransactionEntity::TRANSACTION_DEBT    => 'Debt',
                    TransactionEntity::TRANSACTION_PAYMENT => 'Payment',
                ],
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
            'name'       => 'html',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Show report',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'contractor_type'   => [
                'required' => false,
            ],
            'contractor_id'     => [
                'required' => false,
            ],
            'transaction_route' => [
                'required' => false,
            ],
            'transaction_type'  => [
                'required' => false,
            ],
            'period_begin'      => [
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
            'period_end'        => [
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