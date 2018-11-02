<?php

namespace User\Service\Repository;

use User\Entity;
use User\Exception\ErrorException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator;

class DatabaseAccount {

    const TABLE_USER_ACCOUNTS = 'user_accounts';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var Entity\Account
     */
    protected $prototype;

    /**
     * DatabaseAccount constructor.
     * @param Adapter $adapter
     * @param HydratorInterface $hydrator
     * @param Entity\Account $prototype
     */
    public function __construct(Adapter $adapter, HydratorInterface $hydrator, Entity\Account $prototype) {
        $this->adapter = $adapter;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Выборка учетных записей с разбивкой на страницы
     * @return Paginator\Paginator
     */
    public function fetchAccountsWithPaginator() {
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_USER_ACCOUNTS);
        $select->order('created DESC');
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    /**
     * Выборка учетной записи по уникальному идентификатору пользователя
     * @param int $userId
     * @return Entity\Account
     * @throws ErrorException
     */
    public function fetchOneAccountById(int $userId) {
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_USER_ACCOUNTS);
        $select->where->equalTo('user_id', $userId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->count()) {
            throw new ErrorException('The user account was not received.');
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var Entity\Account $account */
        $account = $resultSet->current();
        return $account;
    }

    /**
     * @param Entity\Account $account
     * @return Entity\Account
     * @throws ErrorException
     */
    public function saveAccount(Entity\Account $account) {
        $data = $this->hydrator->extract($account);
        unset($data['user_id']);
        if ($password = trim($account->getPassword())) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $account->setPassword($data['password']);
        } else {
            unset($data['password']);
        }
        $sql = new Sql($this->adapter, self::TABLE_USER_ACCOUNTS);
        if ($userId = $account->getUserId()) {
            $action = $sql->update();
            $action->set($data);
            $action->where->equalTo('user_id', $userId);
        } else {
            $action = $sql->insert();
            $action->values($data);
        }
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result instanceof ResultInterface || !$result->getAffectedRows()) {
            throw new ErrorException('The user account was not saved.');
        }
        if ($generatedId = $result->getGeneratedValue()) {
            $account->setUserId($generatedId);
        }
        return $account;
    }

    /**
     * Уделение учетной записи по уникальному идентификатору пользователя
     * @param int $userId
     * @throws ErrorException
     * @return void
     */
    public function deleteAccountById(int $userId): void {
        $sql = new Sql($this->adapter);
        $action = $sql->delete(self::TABLE_USER_ACCOUNTS);
        $action->where->equalTo('user_id', $userId);
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result instanceof ResultInterface || !$result->getAffectedRows()) {
            throw new ErrorException('The user account was not deleted.');
        }
        return;
    }

}