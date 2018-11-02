<?php

namespace Manufacturing\Form;


use Manufacturing\Domain\WarehouseEntity;
use Zend\Form\Form;

class SkipCommon extends Form {

    public function init() {

        $this->add([
            'type'    => 'csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1800,
                ],
            ],
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'skip_id',
        ]);

        $this->add([
            'type' => 'hidden',
            'name' => 'company_id',
        ]);

        $this->add([
            'type'    => Element\FurnaceSelect::class,
            'name'    => 'furnace_id',
            'options' => [
                'label' => 'Select furnace',
            ],
        ]);

        $this->add([
            'type'    => Element\WarehouseSelect::class,
            'name'    => 'material_warehouse_id',
            'options' => [
                'warehouse_type' => WarehouseEntity::TYPE_MATERIAL_WAREHOUSE,
                'label'          => 'Select material warehouse',
            ],
        ]);

        $this->add([
            'type'    => Element\WarehouseSelect::class,
            'name'    => 'product_warehouse_id',
            'options' => [
                'warehouse_type' => WarehouseEntity::TYPE_PRODUCT_WAREHOUSE,
                'label'          => 'Select product warehouse',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'date',
            'attributes' => [
                'data-date-format' => 'dd.mm.yyyy',
                'value'            => date('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Date of loading',
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'materials',
            'options' => [
                'label'                  => 'Select materials',
                'count'                  => 2,
                'use_as_base_fieldset'   => false,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Fieldset\SkipMaterial::class,
                ],
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'start_producing',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Start producing',
            ],
        ]);

    }

}