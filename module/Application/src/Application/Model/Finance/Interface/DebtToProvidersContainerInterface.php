<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера записей задолженностей перед поставщиками
 *
 * Interface DebtToProvidersContainerInterface
 * @package Application\Model\Finance
 */
interface DebtToProvidersContainerInterface  extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество записей задолженностей перед поставщиками
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает набор записей задолженностей перед поставщиками
     * @return float
     */
    public function total(): float;
}

