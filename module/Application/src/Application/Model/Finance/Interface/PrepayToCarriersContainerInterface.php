<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера предоплаты перевозчикам
 *
 * Interface PrepayToCarriersContainerInterface
 * @package Application\Model\Finance
 */
interface PrepayToCarriersContainerInterface extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;

    /**
     * Возвращает количество перевозчиков которым предприятие сделало предоплату
     * @return int
     */
    public function count(): int;

    /**
     * Возвращает общую сумму предоплат перевозчикам
     * @return float
     */
    public function total();

}

