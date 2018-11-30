<?php

namespace Transport\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorPlantSelect;
use Zend\Form\Form;

class Rate extends Form {

    /**
     * @throws \Exception
     */
    public function init() {

        $periodBegin = new \DateTime();
        $periodEnd = clone $periodBegin;
        $periodEnd->add(new \DateInterval('P1Y'));

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
            'name' => 'rate_id',
        ]);

        $this->add([
            'type'    => ContractorPlantSelect::class,
            'name'    => 'plant_id',
            'options' => [
                'empty_option' => 'Choose the plant for the rate',
                'label'        => 'Plant for the rate',
            ],
        ]);

        $this->add([
            'type'    => ContractorCompanySelect::class,
            'name'    => 'company_id',
            'options' => [
                'empty_option' => 'Select the company for the rate',
                'label'        => 'Company for the rate',
            ],
        ]);

        $this->add([
            'type'    => Element\StationSelect::class,
            'name'    => 'station_id',
            'options' => [
                'empty_option' => 'Choose the station for the rate',
                'label'        => 'Station for the rate',
            ],
        ]);

        $this->add([
            'type'    => Element\CarrierSelect::class,
            'name'    => 'carrier_id',
            'options' => [
                'empty_option' => 'Choose the carrier for the rate',
                'label'        => 'Carrier for the rate',
            ],
        ]);

        $this->add([
            'type'    => Element\RateDirectionSelect::class,
            'name'    => 'direction',
            'options' => [
                'empty_option' => 'Choose the direction of rate',
                'label'        => 'Direction of rate',
            ],
        ]);

        $this->add([
            'type'    => Element\RateTypeSelect::class,
            'name'    => 'rate_type',
            'options' => [
                'label' => 'Type of rate',
            ],
        ]);

        $this->add([
            'type'    => 'collection',
            'name'    => 'values',
            'options' => [
                'label'                  => 'Please add weights and prices for this rate',
                'count'                  => 1,
                'should_create_template' => true,
                'allow_add'              => true,
                'target_element'         => [
                    'type' => Fieldset\RateValue::class,
                ],
            ],
        ]);

        $this->add([
            'type'       => 'number',
            'name'       => 'min_weight',
            'attributes' => [
                'step' => 0.001,
            ],
            'options'    => [
                'label' => 'Enter the min weight of mixed rate',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_begin',
            'attributes' => [
                'value' => $periodBegin->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period begin',
            ],
        ]);

        $this->add([
            'type'       => 'hidden',
            'name'       => 'period_end',
            'attributes' => [
                'value' => $periodEnd->format('d.m.Y'),
            ],
            'options'    => [
                'label' => 'Period end',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'save_and_recount',
            'attributes' => [
                'class' => 'btn btn-warning',
                'value' => 'Save and recount',
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