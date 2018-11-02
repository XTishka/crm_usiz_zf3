<?php

namespace Application\Service\Repository;

use Application\Exception;
use Application\Domain\TaxEntity;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class TaxDb extends AbstractDb {

    const TABLE_TAXES = 'taxes';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_TAXES);
        return $columnNames;
    }

    public function fetchCurrentTaxValue() {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_TAXES);
        $select->order('created DESC');
        $select->limit(1);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->count())
            return 0;
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAYOBJECT);
        $resultSet->initialize($dataSource);
        return $resultSet->current()->offsetGet('value');
    }

    public function fetchTaxesPaginator($sortColumn = 'tax_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_TAXES);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchTaxesArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_TAXES);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchTaxById(int $taxId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_TAXES);
        $select->where->equalTo('tax_id', $taxId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var TaxEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveTax(TaxEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['tax_id']);
        $sql = new Sql($this->dbAdapter);
        if ($taxId = $object->getTaxId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_TAXES);
            $action->set($data);
            $action->where->equalTo('tax_id', $taxId);
        } else {
            $action = $sql->insert(self::TABLE_TAXES);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Tax data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setTaxId($generatedId);
        return $object;
    }

    public function deleteTaxById(int $taxId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_TAXES);
        $action->where->equalTo('tax_id', $taxId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Tax data was not deleted');
        return;
    }

}