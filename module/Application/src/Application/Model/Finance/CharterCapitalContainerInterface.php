<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера предоплат поставщикам
 *
 * Interface CharterCapitalContainerInterface
 * @package Application\Model\Finance
 */
interface CharterCapitalContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей уставного фонда
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму задолженности уставного фонда
     * @return float
     */
    public function total(): float;
}

