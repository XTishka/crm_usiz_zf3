<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorAdditionalSelect;
use Document\Domain\TransactionEntity;

class TransactionAdditional extends TransactionAbstract {

    public function init() {

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_ADDITIONAL,
            ],
        ]);

        $this->add([
            'type'    => ContractorAdditionalSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select additional contractor',
            ],
        ]);

        parent::init();
    }


}