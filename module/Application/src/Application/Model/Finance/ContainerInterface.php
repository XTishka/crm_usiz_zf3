<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей
 *
 * Interface FincanceContainerInterface
 * @package Application\Model\Finance
 */
interface ContainerInterface extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму
     * @return float
     */
    public function total(): float;
}

