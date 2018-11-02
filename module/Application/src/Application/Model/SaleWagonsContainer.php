<?php
/**
 * Вагоны с продукцией
 */

namespace Application\Model;

use Document\Domain\SaleWagonEntity;

class SaleWagonsContainer implements \IteratorAggregate {

    protected $wagons = [];

    /**
     * PurchaseWagonsContainer constructor.
     * @param array $wagons
     */
    public function __construct(array $wagons) {
        $this->wagons = array_map(function ($data) {
            $wagon = new SaleWagonEntity();
            $wagon->setWagonId($data['wagon_id']);
            $wagon->setContractId($data['contract_id']);
            $wagon->setWagonNumber($data['wagon_number']);
            $wagon->setCarrierId($data['carrier_id']);
            $wagon->setCarrierName($data['carrier_name']);
            $wagon->setProductPrice($data['product_price']);
            $wagon->setRateId($data['rate_id']);
            $wagon->setRateValueId($data['rate_value_id']);
            $wagon->setStatus($data['status']);
            $wagon->setTransportPrice($data['transport_price']);
            $wagon->setLoadingWeight($data['loading_weight']);
            $wagon->setLoadingDate(\DateTime::createFromFormat('Y-m-d', $data['loading_date']));

            return $wagon;
        }, $wagons);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator() {
        return new \ArrayIterator($this->wagons);
    }

    /**
     * Возвращает количество записей
     * @return int
     */
    public function count() {
        return count($this->wagons);
    }

    /**
     * Возвращает общую сумму стоимости сыръя
     * @return int|string
     */
    public function getTotalProductPrice() {
        $total = 0;
        foreach ($this->getIterator() as $wagon) {
            $total = bcadd($total, $wagon->getProductPrice(), 2);
        }
        return $total;
    }

    /**
     * Возвращает общую сумму стоимости доставки
     * @return int|string
     */
    public function getTotalDeliveryPrice() {
        $total = 0;
        foreach ($this->getIterator() as $wagon) {
            $total = bcadd($total, $wagon->getDeliveryPrice(), 2);
        }
        return $total;
    }

    /**
     * Возвращает общий вес загрузки
     * @return int|string
     */
    public function getTotalLoadingWeight() {
        $total = 0;
        foreach ($this->getIterator() as $wagon) {
            $total = bcadd($total, $wagon->getLoadingWeight(), 2);
        }
        return $total;
    }

}