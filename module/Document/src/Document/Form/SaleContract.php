<?php

namespace Document\Form;

use Application\Form\Element\CountrySelect;
use Application\Form\Element\CurrentTaxNumber;
use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorConsigneeSelect;
use Contractor\Form\Element\ContractorCustomerSelect;
use Manufacturing\Form\Element\WarehouseSelect;
use Resource\Form\Element\ProductSelect;
use Transport\Form\Element\CarrierTypeSelect;
use Transport\Form\Element\StationSelect;
use Zend\Form\Form;

class SaleContract extends Form {

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
            'name' => 'contract_id',
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'contract_number',
            'attributes' => [
                'placeholder' => 'Please enter the contract number',
            ],
            'options'    => [
                'label' => 'Contract number',
            ],
        ]);

        $this->add([
            'type'    => ContractorCompanySelect::class,
            'name'    => 'company_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select service company',
            ],
        ]);

        $this->add([
            'type'    => WarehouseSelect::class,
            'name'    => 'warehouse_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select warehouse',
            ],
        ]);

        $this->add([
            'type'    => ProductSelect::class,
            'name'    => 'product_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select product',
            ],
        ]);

        $this->add([
            'type'    => ContractorCustomerSelect::class,
            'name'    => 'customer_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select customer',
            ],
        ]);

        $this->add([
            'type'    => ContractorConsigneeSelect::class,
            'name'    => 'consignee_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select consignee',
            ],
        ]);

        $this->add([
            'type'    => CarrierTypeSelect::class,
            'name'    => 'carrier_type',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select carrier type',
            ],
        ]);

        $this->add([
            'type'    => Element\ConditionsSelect::class,
            'name'    => 'conditions',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select conditions',
            ],
        ]);

        $this->add([
            'type'    => CountrySelect::class,
            'name'    => 'country',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select country of sale',
            ],
        ]);

        $this->add([
            'type'    => StationSelect::class,
            'name'    => 'station_id',
            'options' => [
                'disable_inarray_validator' => true,
                'label'                     => 'Select station',
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'price',
            'attributes' => [
                'min'   => 0,
                'step'  => 0.01,
                'value' => 0.00,
            ],
            'options'    => [
                'label' => 'Price without tax',
            ],
        ]);

        $this->add([
            'type'       => CurrentTaxNumber::class,
            'name'       => 'tax',
            'attributes' => [
                'readonly' => true,
            ],
            'options'    => [
                'label' => 'Current tax rate',
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'created',
            'attributes' => [
                'value'            => date('d.m.Y'),
                'data-date-format' => 'dd.mm.yyyy',
            ],
            'options'    => [
                'label' => 'Date of creating the contract',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_remain',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and remain',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_exit',
            'attributes' => [
                'class' => 'btn btn-success',
                'value' => 'Save and exit',
            ],
        ]);

    }

}