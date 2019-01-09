<?php

namespace Application\Model\Finance;

/**
 * Интерфейс контейнера предоплаты от прочих контрагентов
 *
 * Interface PrepayToOthersContainerInterface
 * @package Application\Model\Finance
 */
interface PrepayToOthersContainerInterface extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;


    /**
     * Возвращает количество прочих контрагентов которым предприятие сделало предоплату
     * @return int
     */
    public function count(): int;


    /**
     * Возвращает общую сумму предоплат прочим контрагентам
     * @return float
     */
    public function total(): float;


}

