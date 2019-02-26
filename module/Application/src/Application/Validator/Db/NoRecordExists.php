<?php

namespace Application\Validator\Db;

class NoRecordExists extends \Zend\Validator\Db\NoRecordExists {

    public function isValid($value, $context = []) {
        $exclude = $this->getExclude();
        if (is_array($exclude)) {
            if (array_key_exists('form_value', $exclude)) {
                $formValue = $exclude['form_value'];
                $exclude['value'] = $context[$formValue];
                $this->setExclude($exclude);
            }
        }
        return parent::isValid($value);
    }

}