<?php

namespace Application\Model\Finance;


/**
 * Интерфейс контейнера общей дебиторской задолженности
 *
 * Interface TotalReceivablesContainerInterface
 * @package Application\Model\Finance
 */
interface TotalReceivablesContainerInterface  {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей общей дебиторской задолженности
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму дебиторской задолженности
     * @return float
     */
    public function total(): float;
}
