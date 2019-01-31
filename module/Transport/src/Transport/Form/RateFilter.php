<?php

namespace Transport\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Contractor\Form\Element\ContractorPlantSelect;
use Transport\Form\Element\CarrierSelect;
use Transport\Form\Element\RateTypeSelect;
use Zend\Form\Form;

class RateFilter extends Form {

    public function init() {

        $this->setAttribute('method', 'get');
        $this->setAttribute('id', 'formRateFilter');

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
            'type'    => CarrierSelect::class,
            'name'    => 'carrier_id',
            'options' => [
                'empty_option' => 'Select the carrier for the rate',
                'label'        => 'Carrier for the rate',
            ],
        ]);

        $this->add([
            'type'    => RateTypeSelect::class,
            'name'    => 'rate_type',
            'options' => [
                'empty_option' => 'Select the type for the rate',
                'label'        => 'Rate for the rate',
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
            'type'    => Element\RateDirectionSelect::class,
            'name'    => 'direction',
            'options' => [
                'empty_option' => 'Choose the direction of rate',
                'label'        => 'Direction of rate',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'filter',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Search rates',
            ],
        ]);

    }

}