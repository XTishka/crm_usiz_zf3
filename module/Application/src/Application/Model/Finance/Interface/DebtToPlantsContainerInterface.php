<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей задолженностей перед заводами
 *
 * Interface DebtToPlantsContainerInterface
 * @package Application\Model\Finance
 */
interface DebtToPlantsContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей задолженностей перед заводами
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму задолженностей перед заводами
     * @return float
     */
    public function total(): float;
}

