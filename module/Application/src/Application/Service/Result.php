<?php

namespace Application\Service;

class Result {

    const STATUS_DEFAULT = 'default';
    const STATUS_ERROR   = 'error';
    const STATUS_INFO    = 'info';
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var mixed
     */
    protected $source;

    /**
     * Result constructor.
     * @param string $status
     * @param string $message
     * @param mixed $source
     */
    public function __construct($status, $message, $source = null) {
        $this->status = $status;
        $this->message = $message;
        $this->source = $source;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return $this->status === self::STATUS_SUCCESS;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getSource() {
        return $this->source;
    }

}