<?php

namespace Resource\Domain;

use Application\Domain\AbstractValueObject;
use Resource\Exception;

class FractionValueObject extends AbstractValueObject {

    /**
     * Минимальный размер фракции в милиметрах
     * @var int
     */
    protected $minSize = 0;

    /**
     * Максимальный размер фракции в милиметрах
     * @var int
     */
    protected $maxSize = 0;

    /**
     * FractionValueObject constructor.
     * @param $minSize
     * @param $maxSize
     */
    public function __construct($minSize, $maxSize) {
        if (!is_numeric($minSize))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a numeric was expected', '$min'));
        if (!is_numeric($maxSize))
            throw new Exception\InvalidArgumentException(sprintf('%s type is not available, a numeric was expected', '$max'));
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
    }

    /**
     * Возвращает минимальный размер фракции в милиметрах
     * @return int
     */
    public function getMinSize(): int {
        return $this->minSize;
    }

    /**
     * Возвращает максимальный размер фракции в милиметрах
     * @return int
     */
    public function getMaxSize(): int {
        return $this->maxSize;
    }

    public function toString() {
        $html = '';
        if ($this->minSize && $this->maxSize)
            $html = sprintf('%d - %d', $this->minSize, $this->maxSize);
        return $html;
    }

}