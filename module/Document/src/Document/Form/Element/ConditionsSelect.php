<?php

namespace Document\Form\Element;

use Document\Domain\PurchaseContractEntity;
use Zend\Form\Element\Select;

class ConditionsSelect extends Select {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);

        $this->setValueOptions([
            PurchaseContractEntity::CONDITIONS_TYPE_FCA        => 'Free Carrier',
            PurchaseContractEntity::CONDITIONS_TYPE_CPT        => 'Carriage Paid To',
            PurchaseContractEntity::CONDITIONS_TYPE_CPT_RETURN => 'Carriage Paid To and Return',
        ]);
    }

}