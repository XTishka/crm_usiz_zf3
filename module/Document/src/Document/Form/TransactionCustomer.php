<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorCustomerSelect;
use Document\Domain\TransactionEntity;

class TransactionCustomer extends TransactionAbstract {

    public function init() {

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_CUSTOMER,
            ],
        ]);

        $this->add([
            'type'    => ContractorCustomerSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select customer contractor',
            ],
        ]);

        parent::init();
    }


}