<?php

namespace Document\Domain;

use DateTime;
use NumberFormatter;

class SaleContractEntity {

    /**
     * Условия доставки товара. Задолженость за товар формируеться перед поставщиком,
     * а за перевозку перед транспортной компанией.
     *
     * Стоимость товара оплачивается поставщику.
     * Стоимость перевозки оплачивается перевозчику.
     */
    const CONDITIONS_TYPE_FCA = 'freeCarrier';

    /**
     * Условия доставки товара. Задолженость формируеться только перед поставщиком за товар.
     *
     * Стоимость товара оплачивается поставщику.
     * Стоимость перевозки не оплачивается.
     */
    const CONDITIONS_TYPE_CPT = 'carriagePaidTo';

    /**
     * Условия доставки товара. Задолженость за товар и перевозку формируеться перед поставщиком
     * поскольку он оплатил доставку, но деньги за доставку нужно вернуть.
     *
     * Стоимость товара оплачивается поставщику.
     * Стоимость перевозки оплачивается поставщику.
     */
    const CONDITIONS_TYPE_CPT_RETURN = 'carriagePaidToAndReturn';

    /**
     * Идентификатор договора
     * @var int
     */
    protected $contractId = 0;

    /**
     * Номер договора. Генерируется автоматически
     * @var string
     */
    protected $contractNumber = '';

    /**
     * Идентификатор обслуживающего предприятия
     * @var int
     */
    protected $companyId = 0;

    /**
     * Название обслуживающего предприятия
     * @var string
     */
    protected $companyName = '';

    /**
     * Идентификатор склада на который будет произведена доставка по договору
     * @var int
     */
    protected $warehouseId = 0;

    /**
     * Название склада на который будет произведена доставка по договору
     * @var string
     */
    protected $warehouseName = '';

    /**
     * Идентификатор поставляемого товара
     * @var int
     */
    protected $productId = 0;

    /**
     * Название поставляемого товара
     * @var string
     */
    protected $productName = '';

    /**
     * Идентификатор покупателя товара
     * @var int
     */
    protected $customerId = 0;

    /**
     * Название покупателя товара
     * @var string
     */
    protected $customerName = '';

    /**
     * Идентификатор грузополучателя
     * @var int
     */
    protected $consigneeId = 0;

    /**
     * Имя грузополучателя
     * @var string
     */
    protected $consigneeName = '';

    /**
     * Тип перевозчика. В текущей реализации существует два типа перевозчиков - железнодорожный транспорт и автотранспорт
     * @var string
     */
    protected $carrierType = '';

    /**
     * Условия доставки товара
     * @var string
     */
    protected $conditions = '';

    /**
     * Страна отправки товара
     * @var string
     */
    protected $country = '';

    /**
     * Station ID
     * @var int
     */
    protected $stationId = 0;

    /**
     * Station name
     * @var string
     */
    protected $stationName = '';

    /**
     * Полное название страны
     * @var string
     */
    protected $countryName = '';

    /**
     * Стоимость договора без НДС
     * @var float
     */
    protected $price = 0.00;

    /**
     * Налоговая ставка
     * @var float
     */
    protected $tax = 0.00;

    /**
     * Количество вагонов в пути
     * @var int
     */
    protected $loadedWagons = 0;


    /**
     * Дата последнего обновления записи
     * @var DateTime
     */
    protected $updated;

    /**
     * Дата создания записи
     * @var DateTime
     */
    protected $created;

    /**
     * Возвращает идентификатор договора
     * @return int
     */
    public function getContractId(): int {
        return $this->contractId;
    }

    /**
     * Устанавливает идентификатор договора
     * @param int $contractId
     */
    public function setContractId(int $contractId) {
        $this->contractId = $contractId;
    }

    /**
     * Возвращает номер договора
     * @return string
     */
    public function getContractNumber(): string {
        return $this->contractNumber;
    }

    /**
     * Устанавливает номер договора
     * @param string $contractNumber
     */
    public function setContractNumber(string $contractNumber) {
        $this->contractNumber = $contractNumber;
    }

    /**
     * Возвращает идентификатор ослуживающего предприятия
     * @return int
     */
    public function getCompanyId(): int {
        return $this->companyId;
    }

    /**
     * Устанавливает идентификатор ослуживающего предприятия
     * @param int $companyId
     */
    public function setCompanyId(int $companyId) {
        $this->companyId = $companyId;
    }

    /**
     * Возвращает название обслуживающего предприятия
     * @return string
     */
    public function getCompanyName(): string {
        return $this->companyName;
    }

    /**
     * Устанавливает название обслуживающего предприятия
     * @param string $companyName
     */
    public function setCompanyName(string $companyName) {
        $this->companyName = $companyName;
    }

    /**
     * Возвращает идентификатор склада
     * @return int
     */
    public function getWarehouseId(): int {
        return $this->warehouseId;
    }

    /**
     * Устанавливает идентификатор склада
     * @param int $warehouseId
     */
    public function setWarehouseId(int $warehouseId) {
        $this->warehouseId = $warehouseId;
    }

    /**
     * Возвращает название склада на который будет произведена доставка по договору
     * @return string
     */
    public function getWarehouseName(): string {
        return $this->warehouseName;
    }

    /**
     * Устанавливает название склада на который будет произведена доставка по договору
     * @param string $warehouseName
     */
    public function setWarehouseName(string $warehouseName) {
        $this->warehouseName = $warehouseName;
    }

    /**
     * Возвращаеит идентифиактор товара
     * @return int
     */
    public function getProductId(): int {
        return $this->productId;
    }

    /**
     * Устанавливает идентификатор товара
     * @param int $productId
     */
    public function setProductId(int $productId) {
        $this->productId = $productId;
    }

    /**
     * Возвращает название товара
     * @return string
     */
    public function getProductName(): string {
        return $this->productName;
    }

    /**
     * Устанавливает название товара
     * @param string $productName
     */
    public function setProductName(string $productName) {
        $this->productName = $productName;
    }

    /**
     * Возвращает идентификатор покупателя
     * @return int
     */
    public function getCustomerId(): int {
        return $this->customerId;
    }

    /**
     * Устанавливает идентификатор покупателя
     * @param int $customerId
     */
    public function setCustomerId(int $customerId) {
        $this->customerId = $customerId;
    }

    /**
     * Возвращает название покупателя
     * @return string
     */
    public function getCustomerName(): string {
        return $this->customerName;
    }

    /**
     * Устанавливает название покупателя
     * @param string $customerName
     */
    public function setCustomerName(string $customerName) {
        $this->customerName = $customerName;
    }

    /**
     * @return int
     */
    public function getConsigneeId(): int {
        return $this->consigneeId;
    }

    /**
     * @param int $consigneeId
     */
    public function setConsigneeId(int $consigneeId): void {
        $this->consigneeId = $consigneeId;
    }

    /**
     * @return string
     */
    public function getConsigneeName(): string {
        return $this->consigneeName;
    }

    /**
     * @param string $consigneeName
     */
    public function setConsigneeName(string $consigneeName): void {
        $this->consigneeName = $consigneeName;
    }

    /**
     * Возвращает тип перевозчика
     * @return string
     */
    public function getCarrierType(): string {
        return $this->carrierType;
    }

    /**
     * Устанавливает тип перевозчика
     * @param string $carrierType
     */
    public function setCarrierType(string $carrierType) {
        $this->carrierType = $carrierType;
    }

    /**
     * Возвращает условия доставки и оплаты
     * @return string
     */
    public function getConditions(): string {
        return $this->conditions;
    }

    /**
     * Устанавливает условия доставки и оплаты
     * @param string $conditions
     */
    public function setConditions(string $conditions) {
        $this->conditions = $conditions;
    }

    /**
     * Возвращает страну отправки товара
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    /**
     * Устанавливает страну отправки товара
     * @param string $country
     */
    public function setCountry(string $country) {
        $this->country = $country;
    }

    /**
     * @return int
     */
    public function getStationId(): int {
        return $this->stationId;
    }

    /**
     * @param int $stationId
     */
    public function setStationId(int $stationId): void {
        $this->stationId = $stationId;
    }

    /**
     * Get station name
     * @return string
     */
    public function getStationName(): string {
        return $this->stationName;
    }

    /**
     * Set station name
     * @param string $stationName
     */
    public function setStationName($stationName): void {
        $this->stationName = (string)$stationName;
    }

    /**
     * Возвращает полное название страны
     * @return string
     */
    public function getCountryName(): string {
        return $this->countryName;
    }

    /**
     * Устанавливает полное название страны
     * @param string $countryName
     */
    public function setCountryName(string $countryName) {
        $this->countryName = $countryName;
    }

    /**
     * Возвращает стоимость по договору
     * @param bool $tax
     * @return float
     */
    public function getPrice($tax = false): float {
        if ($tax) {
            return $this->price + ($this->price * $this->tax * 0.01);
        }
        return $this->price;
    }

    /**
     * Устанавливает стоимость по договору
     * @param float $price
     */
    public function setPrice(float $price) {
        $this->price = $price;
    }

    /**
     * Возвращает налоговую ставку в процентах
     * @return float
     */
    public function getTax(): float {
        return $this->tax;
    }

    /**
     * Устанавливает налоговую ставку в процентах
     * @param float $tax
     */
    public function setTax(float $tax) {
        $this->tax = $tax;
    }

    /**
     * @return int
     */
    public function getLoadedWagons(): int {
        return $this->loadedWagons;
    }

    /**
     * @param int $loadedWagons
     */
    public function setLoadedWagons(int $loadedWagons): void {
        $this->loadedWagons = $loadedWagons;
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