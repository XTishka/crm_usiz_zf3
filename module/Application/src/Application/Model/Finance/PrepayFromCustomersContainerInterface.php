<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей предоплаты от покупателей
 *
 * Interface PrepayFromCustomersContainerInterface
 * @package Application\Model\Finance
 */
interface PrepayFromCustomersContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей предоплаты от покупателей
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму задолженности перед покупателями
     * @return float
     */
    public function total(): float;
}

