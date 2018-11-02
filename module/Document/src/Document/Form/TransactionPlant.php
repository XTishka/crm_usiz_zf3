<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorPlantSelect;
use Document\Domain\TransactionEntity;

class TransactionPlant extends TransactionAbstract {

    public function init() {

        parent::init();

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_PLANT,
            ],
        ]);

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select plant contractor',
            ],
        ]);

    }


}