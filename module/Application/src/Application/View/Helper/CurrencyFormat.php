<?php

namespace Application\View\Helper;

class CurrencyFormat extends \Zend\I18n\View\Helper\CurrencyFormat {

    protected function formatCurrency($number, $currencyCode, $showDecimals, $locale, $pattern) {
        $value = parent::formatCurrency($number, $currencyCode, $showDecimals, $locale, $pattern);
        $value = preg_replace('/^([\s\S]+)(₴)+$/i', '${1}грн.', $value);
        return $value;
    }

}