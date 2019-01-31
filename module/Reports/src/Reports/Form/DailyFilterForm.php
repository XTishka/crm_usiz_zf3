<?php

namespace Reports\Form;

use Contractor\Form\Element\ContractorCompanySelect;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class DailyFilterForm extends Form implements InputFilterProviderInterface {

    /**
     * @throws \Exception
     */
    public function init() {

        $this->setAttribute('method', 'post');
        $this->setAttribute('id', 'formShipmentsFilter');

        $periodBegin = new \DateTime();
        $periodEnd   = clone $periodBegin;
        $periodEnd->add(new \DateInterval('P10D'));

        $this->add([
            'type'       => ContractorCompanySelect::class,
            'name'       => 'company_id',
            'attributes' => [
                'class' => 'input-inline',
                'style' => 'max-width:150px',
            ],
            'options'    => [
                'label'            => 'Select company',
                'label_attributes' => [
                    'class' => 'label-inline',
                ],
            ],
        ]);

        $this->add([
            'type'       => 'text',
            'name'       => 'date',
            'attributes' => [
                'value'        => $periodBegin->format('d.m.Y'),
                'class'        => 'input-inline',
                'autocomplete' => 'off',
                'size'         => 10,
            ],
            'options'    => [
                'label'            => 'Date',
                'label_attributes' => [
                    'class' => 'label-inline',
                ],
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-info',
                'value' => 'Download report',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'submit',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Download report',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'html',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Show report',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [
            'company_id'   => [
                'required' => true,
                'filters'  => [
                    ['name' => 'ToInt'],
                ],
            ],
            'period_begin' => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format' => 'd.m.Y',
                    ]],
                ],
            ],
            'period_end'   => [
                'required'   => false,
                'filters'    => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Date', 'break_chain_on_failure' => true, 'options' => [
                        'format' => 'd.m.Y',
                    ]],
                ],
            ],
        ];
    }


}