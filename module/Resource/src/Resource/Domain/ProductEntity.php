<?php

namespace Resource\Domain;

use DateTime;

class ProductEntity {

    /**
     * Идентификатор продукта
     * @var int
     */
    protected $productId = 0;

    /**
     * Название продукта
     * @var string
     */
    protected $productName = '';

    /**
     * Описание сыръя
     * @var string
     */
    protected $description = '';

    /**
     * Дата последнего обновления записи тарифной ставки
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата создания записи тарифной ставки
     * @var DateTime
     */
    protected $created;

    /**
     * Возвращает идентификатор продукта
     * @return int
     */
    public function getProductId(): int {
        return $this->productId;
    }

    /**
     * Устанавливает идентификатор продукта
     * @param int $productId
     */
    public function setProductId(int $productId) {
        $this->productId = $productId;
    }

    /**
     * Возвращает название сыръя
     * @return string
     */
    public function getProductName(): string {
        return $this->productName;
    }

    /**
     * Устанавливает название сыръя
     * @param string $productName
     */
    public function setProductName(string $productName) {
        $this->productName = $productName;
    }

    /**
     * Возвращает описание продукта
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Устанавливает описание продукта
     * @param string $description
     */
    public function setDescription(string $description) {
        $this->description = $description;
    }

    /**
     * Возвращает объект даты и времени обновления текущей записи, если значение пустое,
     * тогда будет возвращен объект DateTime с текущим значением даты и времени.
     * @return DateTime
     */
    public function getUpdated(): DateTime {
        if (!$this->updated instanceof DateTime)
            $this->updated = new DateTime($this->updated);
        return $this->updated;
    }

    /**
     * Устанавливает объект даты и времени обновления текущей записи.
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated) {
        $this->updated = $updated;
    }

    /**
     * Возвращает объект даты и времени создания текущей записи, если значение пустое,
     * тогда будет возвращен объект DateTime с текущим значением даты и времени.
     * @return DateTime
     */
    public function getCreated(): DateTime {
        if (!$this->created instanceof DateTime)
            $this->created = new DateTime($this->created);
        return $this->created;
    }

    /**
     * Устанавливает объект даты и времени создания текущей записи.
     * @param DateTime $created
     */
    public function setCreated(DateTime $created) {
        $this->created = $created;
    }

}
