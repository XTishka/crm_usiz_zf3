<?php

namespace Manufacturing\Form\Element;

use Manufacturing\Domain\WarehouseEntity;
use Zend\Form\Element\Select;

class WarehouseTypeSelect extends Select {

    public function __construct($name = null, array $options = []) {
        parent::__construct($name, $options);
        $options = [
            WarehouseEntity::TYPE_MATERIAL_WAREHOUSE => 'Material warehouse',
            WarehouseEntity::TYPE_PRODUCT_WAREHOUSE  => 'Product warehouse',
        ];
        $this->setValueOptions($options);
    }

}