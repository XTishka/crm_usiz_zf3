<?php

namespace Application\Service\Repository;

use ArrayObject;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Adapter\Driver\ConnectionInterface;
use Zend\Hydrator\HydratorInterface;

abstract class AbstractDb {

    /**
     * @var DbAdapter
     */
    protected $dbAdapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var mixed
     */
    protected $prototype;

    /**
     * AbstractDb constructor.
     * @param DbAdapter $dbAdapter
     * @param HydratorInterface $hydrator
     * @param mixed $prototype
     */
    public function __construct(DbAdapter $dbAdapter, HydratorInterface $hydrator, $prototype = null) {
        $this->dbAdapter = $dbAdapter;
        $this->hydrator = clone $hydrator;
        $this->prototype = $prototype ?? new ArrayObject();
    }

    /**
     * @return DbAdapter
     */
    public function getDbAdapter(): DbAdapter {
        return $this->dbAdapter;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator(): HydratorInterface {
        return $this->hydrator;
    }

    /**
     * @return mixed
     */
    public function getPrototype() {
        return $this->prototype;
    }

    /**
     * @return ConnectionInterface
     */
    public function beginTransaction() {
        $connection = $this->dbAdapter->getDriver()->getConnection();
        return $connection->beginTransaction();
    }

    /**
     * @return ConnectionInterface
     */
    public function commit() {
        $connection = $this->dbAdapter->getDriver()->getConnection();
        return $connection->commit();
    }

    /**
     * @return ConnectionInterface
     */
    public function rollback() {
        $connection = $this->dbAdapter->getDriver()->getConnection();
        return $connection->rollback();
    }

}