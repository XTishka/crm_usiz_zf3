<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;


/**
 * Интерфейс контейнера задолженности покупателей
 *
 * Interface CustomerReceivableContainerInterface
 * @package Application\Model\Finance
 */
interface CustomerReceivableContainerInterface extends \IteratorAggregate {

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable;

    /**
     * Возвращает количество покупателей имеющих задолженность
     * @return int
     */
    public function count(): int;

    /**
     * Возвращает общую сумму задолженности от покупателей
     * @return float
     */
    public function total();

}
