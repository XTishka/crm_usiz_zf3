<?php

namespace Document\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorPlantSelect;
use Document\Domain\TransactionEntity;

class TransactionCompany extends TransactionAbstract {

    public function init() {

        parent::init();

        $this->add([
            'type'       => 'hidden',
            'name'       => 'contractor_type',
            'attributes' => [
                'value' => TransactionEntity::CONTRACTOR_COMPANY,
            ],
        ]);

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'company_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select plant contractor',
            ],
        ]);

        $this->add([
            'type'    => ContractorCompanySelect::class,
            'name'    => 'contractor_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select company contractor',
            ],
        ]);

    }


}