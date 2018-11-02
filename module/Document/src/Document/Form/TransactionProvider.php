<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorProviderSelect;
use Document\Domain\TransactionEntity;

class TransactionProvider extends TransactionAbstract {

    public function init() {

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_PROVIDER,
            ],
        ]);

        $this->add([
            'type'    => ContractorProviderSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label' => 'Select provider contractor',
            ],
        ]);

        parent::init();
    }


}