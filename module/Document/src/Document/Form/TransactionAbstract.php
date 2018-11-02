<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorPlantSelect;
use Document\Domain\TransactionEntity;
use Zend\Form\Form;

abstract class TransactionAbstract extends Form {

    public function init() {

        $this->add([
            'type' => 'hidden',
            'name' => 'transaction_id',
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'is_manual',
            'attributes' => [
                'value' => 1,
            ],
        ]);

        $this->add([
            'type'    => ContractorCompanySelect::class,
            'name'    => 'company_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select company',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'direction',
            'options' => [
                'label'         => 'Select transaction direction',
                'value_options' => [
                    'credit' => 'Credit transaction',
                    'debit'  => 'Debit transaction',
                ],
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'created',
            'attributes' => [
                'value' => date('d.m.Y H:i:s')
            ],
            'options' => [
                'label' => 'Transaction date'
            ]
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'credit',
            'attributes' => [
                'step' => 0.01,
                'min'  => 0,
            ],
            'options'    => [
                'label' => 'Amount of transaction',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'debit',
            'attributes' => [
                'step' => 0.01,
                'min'  => 0,
            ],
            'options'    => [
                'label' => 'Amount of transaction',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'transaction_type',
            'options' => [
                'label'         => 'Select transaction type',
                'value_options' => [
                    TransactionEntity::TRANSACTION_PAYMENT => 'Payment',
                    TransactionEntity::TRANSACTION_DEBT    => 'Debt',
                ],
            ],
        ]);

        $this->add([
            'type'       => 'textarea',
            'name'       => 'comment',
            'attributes' => [
                'rows' => 3,
            ],
            'options'    => [
                'label' => 'Comment',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save transaction',
            ],
        ]);

    }

}