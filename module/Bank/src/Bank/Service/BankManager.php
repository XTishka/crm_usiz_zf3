<?php

namespace Bank\Service;

use Bank\Entity\BankEntity;
use Bank\Exception\DeleteErrorException;
use Bank\Exception\NotFoundException;
use Bank\Exception\SaveErrorException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class BankManager {

    const TABLE_BANKS = 'banks';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * BankManager constructor.
     * @param Adapter           $adapter
     * @param HydratorInterface $hydrator
     */
    public function __construct(Adapter $adapter, HydratorInterface $hydrator) {
        $this->adapter = $adapter;
        $this->hydrator = $hydrator;
    }

    /**
     * @return Paginator
     * @throws NotFoundException
     */
    public function fetchAllWithPaginator() {
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_BANKS);
        $select->order('name ASC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. Bank data not received');
        $resultSet = new HydratingResultSet($this->hydrator, new BankEntity());
        $paginator = new Paginator(new DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    /**
     * @param $bankId
     * @return BankEntity
     * @throws NotFoundException
     */
    public function fetchOneById($bankId) {
        $bankId = intval($bankId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_BANKS);
        $select->where->equalTo('bank_id', $bankId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult() || !$dataSource->count())
            throw new NotFoundException('No banks found');
        $resultSet = new HydratingResultSet($this->hydrator, new BankEntity());
        $resultSet->initialize($dataSource);
        /** @var BankEntity $bankEntity */
        $bankEntity = $resultSet->current();
        return $bankEntity;
    }

    /**
     * @return array
     * @throws NotFoundException
     */
    public function fetchValueOptions() {
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_BANKS);
        $select->columns(['bank_id', 'name']);
        $select->order('name ASC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult() || !$dataSource->count())
            throw new NotFoundException('No banks found');
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);
        $valueOptions = [];
        foreach ($resultSet as $value) {
            $valueOptions[$value->bank_id] = $value->name;
        }
        return $valueOptions;
    }

    /**
     * @param BankEntity $bankEntity
     * @return BankEntity
     * @throws SaveErrorException
     */
    public function saveBank(BankEntity $bankEntity) {
        $data = $this->hydrator->extract($bankEntity);
        $sql = new Sql($this->adapter, self::TABLE_BANKS);
        if ($bankId = $bankEntity->getBankId()) {
            unset($data['created']);
            $action = $sql->update();
            $action->set($data);
            $action->where->equalTo('bank_id', $bankId);
        } else {
            $action = $sql->insert();
            $action->values($data);
        }
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->getAffectedRows())
            throw new SaveErrorException('Unknown error. The bank data was not saved');
        if ($bankId = $result->getGeneratedValue())
            $bankEntity->setBankId($bankId);
        return $bankEntity;
    }


    /**
     * @param int $bankId
     * @throws DeleteErrorException
     */
    public function deleteBank(int $bankId) {
        $bankId = intval($bankId);
        $sql = new Sql($this->adapter, self::TABLE_BANKS);
        $action = $sql->delete();
        $action->where->equalTo('bank_id', $bankId);
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->isQueryResult() || !$result->getAffectedRows())
            throw new DeleteErrorException('Unknown error. The bank data was not deleted');
        return;
    }







}