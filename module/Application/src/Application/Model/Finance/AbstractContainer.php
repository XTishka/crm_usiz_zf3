<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

/**
 * Контейнер записей
 *
 * Class AccountsPayableContainer
 * @package Application\Model\Finance
 */
abstract class AbstractContainer implements ContainerInterface {

    protected $records = [];

    /**
     * AbstractContainer constructor.
     * @param array $records
     */
    public function __construct(array $records) {
        $this->records = $records;
    }

    /**
     * Возвращает итератор
     * @return \Traversable
     */
    public function getIterator(): \Traversable {
        return new \ArrayIterator($this->records);
    }

    /**
     * Возвращает количество
     * @return int
     */
    public function count(): int {
        return count($this->records);
    }

    /**
     * Возвращает общую сумму
     * @return float
     */
    public function total(): float {
        $total = 0;
        foreach ($this->getIterator() as $record) {
            $total = bcadd($total, $record['amount'], 2);
        }
        return $total;
    }


}