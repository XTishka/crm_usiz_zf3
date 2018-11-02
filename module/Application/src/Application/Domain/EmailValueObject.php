<?php

namespace Application\Domain;

use Application\Exception;

class EmailValueObject extends AbstractValueObject {

    protected $email = '';

    protected $description = '';

    public function __construct($email, $description = '') {
        if (!is_string($email))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new Exception\InvalidArgumentException('Invalid email address');
        if (!is_string($description))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a string was expected', '$description'));
        $this->email = $email;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

}