<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей общей кредиторской задолженности
 *
 * Interface AccountsPayableContainerInterface
 * @package Application\Model\Finance
 */
interface AccountsPayableContainerInterface  extends \IteratorAggregate {

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

