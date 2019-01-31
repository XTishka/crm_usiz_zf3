<?php

namespace Contractor\Service\Repository;

use Contractor\Entity\ContractorAbstract;
use Contractor\Exception\ErrorException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

abstract class DatabaseContractorAbstract {

    const TABLE_CONTRACTORS = 'contractors';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var ContractorAbstract
     */
    protected $prototype;

    /**
     * @var string
     */
    protected $contractorType = null;

    /**
     * DatabaseContractorAbstract constructor.
     *
     * @param Adapter            $adapter
     * @param HydratorInterface  $hydrator
     * @param ContractorAbstract $prototype
     */
    public function __construct(Adapter $adapter, HydratorInterface $hydrator, ContractorAbstract $prototype) {
        $this->adapter   = $adapter;
        $this->hydrator  = $hydrator;
        $this->prototype = $prototype;
    }

    public function fetchAllContractorsWithPaginator($queryParams = null) {
        $sql    = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_CONTRACTORS);
        $select->order(['contractor_type ASC', 'contractor_name ASC']);

        if (is_array($queryParams)) {
            if (key_exists('contractor_type', $queryParams) && trim($queryParams['contractor_type'])) {
                $select->where->equalTo('contractor_type', $queryParams['contractor_type']);
            }
            if (key_exists('contractor_name', $queryParams) && trim($queryParams['contractor_name'])) {
                $select->where->like('contractor_name', sprintf('%%%s%%', $queryParams['contractor_name']));
            }
            if (key_exists('register_code', $queryParams) && trim($queryParams['register_code'])) {
                $select->where->equalTo('register_code', $queryParams['register_code']);
            }
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        return new Paginator(new DbSelect($select, $sql, $resultSet));
    }

    /**
     * @return Paginator
     */
    public function fetchContractorsWithPaginator() {
        $sql    = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_CONTRACTORS);
        $select->where->equalTo('contractor_type', $this->contractorType);
        $select->order('contractor_name ASC');
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        return new Paginator(new DbSelect($select, $sql, $resultSet));
    }

    /**
     * @return array
     */
    public function fetchContractorsValueOptions() {
        $sql    = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_CONTRACTORS);
        $select->columns(['contractor_id', 'contractor_name', 'plant_id']);
        $select->where->equalTo('contractor_type', $this->contractorType);
        $select->order('contractor_name ASC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet  = new ResultSet('array');
        $resultSet->initialize($dataSource);

        $valueOptions = array_map(function ($contractorData) {
            return [
                'label'      => $contractorData['contractor_name'],
                'value'      => $contractorData['contractor_id'],
                'attributes' => [
                    'data-plant' => $contractorData['plant_id'],
                ],
            ];
        }, $resultSet->toArray());

        return $valueOptions;
    }

    /**
     * @param int $contractorId
     *
     * @return ContractorAbstract
     * @throws ErrorException
     */
    public function fetchContractorById(int $contractorId) {
        $sql    = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_CONTRACTORS);
        $select->where->equalTo('contractor_type', $this->contractorType);
        $select->where->equalTo('contractor_id', $contractorId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->count()) {
            throw new ErrorException('Contractor data were not received.');
        }
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var ContractorAbstract $contractor */
        $contractor = $resultSet->current();
        return $contractor;
    }

    /**
     * @param ContractorAbstract $object
     *
     * @return ContractorAbstract
     * @throws ErrorException
     */
    public function saveContractor(ContractorAbstract $object) {
        $sql  = new Sql($this->adapter, self::TABLE_CONTRACTORS);
        $data = $this->hydrator->extract($object);

        if ($contractorId = $object->getContractorId()) {
            $action = $sql->update();
            $action->set($data);
            $action->where->equalTo('contractor_type', $this->contractorType);
            $action->where->equalTo('contractor_id', $contractorId);
        }
        else {
            $action = $sql->insert();
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface) {
            throw new ErrorException('Contractor data were not saved.');
        }
        if ($generatedId = $dataSource->getGeneratedValue()) {
            $object->setContractorId($generatedId);
        }
        return $object;
    }

    /**
     * @param int $contractorId
     *
     * @return void
     * @throws ErrorException
     */
    public function deleteContractorById(int $contractorId) {
        $sql    = new Sql($this->adapter, self::TABLE_CONTRACTORS);
        $action = $sql->delete();
        $action->where->equalTo('contractor_type', $this->contractorType);
        $action->where->equalTo('contractor_id', $contractorId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface) {
            throw new ErrorException('Contractor data were not saved.');
        }
        return;
    }

}