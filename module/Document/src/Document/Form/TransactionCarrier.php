<?php

namespace Document\Form;

use Document\Domain\TransactionEntity;
use Transport\Form\Element\CarrierSelect;

class TransactionCarrier extends TransactionAbstract {

    public function init() {

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_CARRIER,
            ],
        ]);

        $this->add([
            'type'    => CarrierSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier contractor',
            ],
        ]);

        parent::init();

        $this->remove('company_id');

        $this->add([
            'type' => 'hidden',
            'name' => 'company_id',
        ]);

    }


}