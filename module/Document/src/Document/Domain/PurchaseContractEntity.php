<?php

namespace Document\Domain;

use DateTime;
use NumberFormatter;

class PurchaseContractEntity {

    /**
     * Условия доставки сырья. Задолженость за сырье формируеться перед поставщиком,
     * а за перевозку перед транспортной компанией.
     *
     * Стоимость сырья оплачивается поставщику.
     * Стоимость перевозки оплачивается перевозчику.
     */
    const CONDITIONS_TYPE_FCA = 'freeCarrier';

    /**
     * Условия доставки сырья. Задолженость формируеться только перед поставщиком за сырье.
     *
     * Стоимость сырья оплачивается поставщику.
     * Стоимость перевозки не оплачивается.
     */
    const CONDITIONS_TYPE_CPT = 'carriagePaidTo';

    /**
     * Условия доставки сырья. Задолженость за сырье и перевозку формируеться перед поставщиком
     * поскольку он оплатил доставку, но деньги за доставку нужно вернуть.
     *
     * Стоимость сырья оплачивается поставщику.
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
     * Идентификатор поставляемого сырья
     * @var int
     */
    protected $materialId = 0;

    /**
     * Название поставляемого сырья
     * @var string
     */
    protected $materialName = '';

    /**
     * Идентификатор поставщика сырья
     * @var int
     */
    protected $providerId = 0;

    /**
     * Название поставщика сырья
     * @var string
     */
    protected $providerName = '';

    /**
     * Тип перевозчика. В текущей реализации существует два типа перевозчиков - железнодорожный транспорт и автотранспорт
     * @var string
     */
    protected $carrierType = '';

    /**
     * Условия доставки сырья
     * @var string
     */
    protected $conditions = '';

    /**
     * Страна отправки сырья
     * @var string
     */
    protected $country = '';

    /**
     * Полное название страны
     * @var string
     */
    protected $countryName = '';

    /**
     * @var int
     */
    protected $stationId = 0;

    /**
     * @var string
     */
    protected $stationName = '';

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
     * Количество разгруженных вагонов
     * @var int
     */
    protected $unloadedWagons = 0;


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
    public function getContractNumber() {
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
     * Возвращаеит идентифиактор сырья
     * @return int
     */
    public function getMaterialId(): int {
        return $this->materialId;
    }

    /**
     * Устанавливает идентификатор сырья
     * @param int $materialId
     */
    public function setMaterialId(int $materialId) {
        $this->materialId = $materialId;
    }

    /**
     * Возвращает название сырья
     * @return string
     */
    public function getMaterialName() {
        return $this->materialName;
    }

    /**
     * Устанавливает название сырья
     * @param string $materialName
     */
    public function setMaterialName(string $materialName) {
        $this->materialName = $materialName;
    }

    /**
     * Возвращает идентификатор поставщика
     * @return int
     */
    public function getProviderId(): int {
        return $this->providerId;
    }

    /**
     * Устанавливает идентификатор поставщика
     * @param int $providerId
     */
    public function setProviderId(int $providerId) {
        $this->providerId = $providerId;
    }

    /**
     * Возвращает название поставщика
     * @return string
     */
    public function getProviderName(): string {
        return $this->providerName;
    }

    /**
     * Устанавливает название поставщика
     * @param string $providerName
     */
    public function setProviderName(string $providerName) {
        $this->providerName = $providerName;
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
     * @return string
     */
    public function getStationName(): string {
        return $this->stationName;
    }

    /**
     * @param string $stationName
     */
    public function setStationName($stationName): void {
        $this->stationName = (string)$stationName;
    }

    /**
     * Возвращает страну отправки сырья
     * @return string
     */
    public function getCountry(): string {
        return $this->country;
    }

    /**
     * Устанавливает страну отправки сырья
     * @param string $country
     */
    public function setCountry(string $country) {
        $this->country = $country;
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
            return round(bcadd($this->price, bcmul($this->price, bcdiv($this->tax, 100, 10), 10), 10), 2);
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
    public function setLoadedWagons($loadedWagons): void {
        $this->loadedWagons = (int)$loadedWagons;
    }

    /**
     * @return int
     */
    public function getUnloadedWagons(): int {
        return $this->unloadedWagons;
    }

    /**
     * @param int $unloadedWagons
     */
    public function setUnloadedWagons($unloadedWagons): void {
        $this->unloadedWagons = (int)$unloadedWagons;
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