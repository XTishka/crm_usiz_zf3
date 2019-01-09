<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера предоплат поставщикам
 *
 * Interface CustomerReceivablesServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayToProvidersContainerInterface extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество поставщиков которым предприятие сделало предоплату
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму предоплат поставщикам
     * @return float
     */
    public function total(): float;
}

