<?php

namespace Contractor\Form;

use Contractor\Form\Element\ContractorPlantSelect;

class ContractorCompany extends ContractorAbstract {

    public function init() {

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'plant_id',
            'options' => [
                'label' => 'Select plant',
            ],
        ]);

        parent::init();
    }

}