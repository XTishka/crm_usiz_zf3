<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

/**
 * Контейнер записей
 *
 * Class AccountsPayableContainer
 *
 * @package Application\Model\Finance
 */
abstract class AbstractContainer implements ContainerInterface {

    /**
     * @var callable
     */
    protected $filter;

    protected $records = [];

    /**
     * AbstractContainer constructor.
     *
     * @param array $records
     */
    public function __construct(array $records) {
        $this->records = $records;
    }

    /**
     * @return callable
     */
    public function getFilter(): callable {
        return $this->filter;
    }

    /**
     * @param callable $filter
     */
    public function setFilter(callable $filter): void {
        $this->filter = $filter;
    }

    /**
     * Возвращает итератор
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable {
        $iterator = new \ArrayIterator($this->records);
        if (!is_callable($this->filter)) {
            return $iterator;
        }

        $filtered = new \CallbackFilterIterator($iterator, $this->filter);
        return $filtered;
    }

    /**
     * Возвращает количество
     *
     * @return int
     */
    public function count(): int {
        return count($this->records);
    }

    /**
     * Возвращает общую сумму
     *
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