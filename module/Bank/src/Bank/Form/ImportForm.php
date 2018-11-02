<?php

namespace Bank\Form;


use Zend\Form\Form;

class ImportForm extends Form {

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

}