<?php

namespace Application\Domain;

use Application\Exception\InvalidArgumentException;

class AddressValueObject extends AbstractValueObject {

    protected $streetName = '';

    protected $streetNumber = '';

    protected $city = '';

    protected $state = '';

    protected $postCode = '';

    protected $country = '';


    public function __construct($streetName, $streetNumber, $city, $state, $postCode = '', $country = '') {
        if (!is_string($streetName))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$streetName'));
        if (!is_string($streetNumber))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$streetNumber'));
        if (!is_string($city))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$city'));
        if (!is_string($state))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$state'));
        if (!is_string($postCode))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$postCode'));
        if (!is_string($country))
            throw new InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$country'));
        $this->streetName = trim($streetName);
        $this->streetNumber = trim($streetNumber);
        $this->city = trim($city);
        $this->state = trim($state);
        $this->postCode = trim($postCode);
        $this->country = trim($country);
    }

    /**
     * @return string
     */
    public function getStreetName(): string {
        return $this->streetName;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string {
        return $this->streetNumber;
    }

    /**
     * @return string
     */
    public function getCity(): string {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState(): string {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPostCode(): string {
        return $this->postCode;
    }

    /**
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    public function toString() {
        $filtered = array_filter($this->toArray(), 'trim');
        return join(', ', $filtered);
    }

}