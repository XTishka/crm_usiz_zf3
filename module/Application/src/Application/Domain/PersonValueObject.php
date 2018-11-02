<?php

namespace Application\Domain;

use Application\Exception;

class PersonValueObject extends AbstractValueObject {

    /**
     * @var string
     */
    protected $firstName = '';

    /**
     * @var string
     */
    protected $middleName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * Person constructor.
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     */
    public function __construct(string $firstName, string $lastName, string $middleName = '') {
        if (!is_string($firstName))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$firstName'));
        if (!is_string($lastName))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$lastName'));
        if (!is_string($middleName))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$middleName'));
        $this->firstName = trim($firstName);
        $this->middleName = trim($middleName);
        $this->lastName = trim($lastName);
    }

    /**
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

    public function toString() {
        return join(' ', array_filter($this->toArray(), 'trim'));
    }

}