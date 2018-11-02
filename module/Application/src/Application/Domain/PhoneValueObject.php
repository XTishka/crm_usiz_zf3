<?php

namespace Application\Domain;

use Application\Exception;
use Zend\I18n\Validator\PhoneNumber;
use Zend\Validator\StaticValidator;

class PhoneValueObject extends AbstractValueObject {

    protected $number = '';

    protected $description = '';

    protected $country = '';

    public function __construct($number, $description = '', $country = 'UA') {
        if (!is_string($country))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$country'));
        if (!is_string($number))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$number'));
        if (!StaticValidator::execute($number, PhoneNumber::class, ['country' => $country]))
            throw new Exception\InvalidArgumentException('Invalid phone number format');
        if (!is_string($description))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$description'));
        $this->country = $country;
        $this->description = $description;
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber(): string {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

}