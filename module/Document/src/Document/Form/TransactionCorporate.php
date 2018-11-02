<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorCorporateSelect;
use Document\Domain\TransactionEntity;

class TransactionCorporate extends TransactionAbstract {

    public function init() {

        parent::init();

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_CORPORATE,
            ],
        ]);

        $this->add([
            'type'    => ContractorCorporateSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select corporate contractor',
            ],
        ]);

        $this->add([
            'type'    => 'select',
            'name'    => 'transaction_type',
            'options' => [
                'label'         => 'Select transaction type',
                'value_options' => [
                    TransactionEntity::TRANSACTION_PAYMENT => 'Payment',
                ],
            ],
        ]);

    }


}