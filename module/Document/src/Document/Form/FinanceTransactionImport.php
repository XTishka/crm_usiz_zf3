<?php

namespace Document\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class FinanceTransactionImport extends Form implements InputFilterProviderInterface {

    public function init() {

        $this->add([
            'type'    => 'file',
            'name'    => 'file',
            'options' => [
                'label' => 'Select file to upload',
            ],
        ]);

        $this->add([
            'type'       => 'submit',
            'name'       => 'import',
            'attributes' => [
                'class' => 'btn btn-primary',
                'value' => 'Import',
            ],
        ]);

    }

    public function getInputFilterSpecification() {
        return [

        ];
    }

}