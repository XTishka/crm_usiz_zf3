<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера предоплаты заводам
 *
 * Interface PrepayToPlantsContainerInterface
 * @package Application\Model\Finance
 */
interface PrepayToPlantsContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество заводов которым предприятие сделало предоплату
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму предоплат заводам
     * @return float
     */
    public function total(): float;


}

