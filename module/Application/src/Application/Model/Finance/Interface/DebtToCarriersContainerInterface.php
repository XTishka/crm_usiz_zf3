<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей задолженностей перед перевозчиками
 *
 * Interface DebtToCarriersContainerInterface
 * @package Application\Model\Finance
 */
interface DebtToCarriersContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей задолженностей перед перевозчиками
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму задолженностей перед перевозчиками
     * @return float
     */
    public function total(): float;
}

