<?php

namespace Manufacturing\Form\Element;

use Zend\Form\Element\Select;

class WarehouseSelect extends Select {

    public function getValueOptions() {
        if (key_exists('warehouse_type', $this->options)) {
            $valueOptions = array_filter($this->valueOptions, function ($option) {
                return (
                    key_exists('attributes', $option) &&
                    key_exists('data-type', $option['attributes']) &&
                    $option['attributes']['data-type'] == $this->options['warehouse_type']);
            });
            return $valueOptions;
        }
        return $this->valueOptions;
    }

}