<?php
/**
 * Дебиторская задолженность
 */

namespace Application\Model;

class AccountsReceivableContainer implements \IteratorAggregate {

    protected $records = [];

    /**
     * AccountsPayableContainer constructor.
     * @param array $records
     */
    public function __construct(array $records) {
        $this->records = $records;
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator() {
        return new \ArrayIterator($this->records);
    }

    /**
     * Возвращает количество записей
     * @return int
     */
    public function count() {
        return count($this->records);
    }

    /**
     * Возвращает общую сумму записей
     * @return int|string
     */
    public function getTotal() {
        $total = 0;
        foreach ($this->getIterator() as $record) {
            $total = bcadd($total, $record['amount'], 2);
        }
        return $total;
    }


}