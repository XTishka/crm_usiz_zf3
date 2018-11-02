<?php

namespace Bank\Form;

use Zend\Form\Form;

class FilterForm extends Form {

    /**
     * @throws \Exception
     */
    public function init() {

        $this->setAttribute('method', 'get');
        $this->setAttribute('id', 'formRecordFilter');

        $periodBegin = new \DateTime();
        $periodEnd = clone $periodBegin;
        $periodEnd->add(new \DateInterval('P10D'));

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
            'name'       => 'filter',
            'attributes' => [
                'class' => 'btn btn-info',
                'value' => 'Apply filter',
            ],
        ]);

    }

}