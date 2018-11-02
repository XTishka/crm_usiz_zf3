<?php

namespace Resource\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Resource\Domain\MaterialEntity;
use Resource\Exception;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class MaterialDb extends AbstractDb {

    const TABLE_MATERIALS = 'materials';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_MATERIALS);
        return $columnNames;
    }

    public function fetchMaterialsPaginator($sortColumn = 'material_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_MATERIALS);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchMaterialsArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_MATERIALS);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchMaterialById(int $materialId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_MATERIALS);
        $select->where->equalTo('material_id', $materialId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var MaterialEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveMaterial(MaterialEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['material_id']);
        $sql = new Sql($this->dbAdapter);
        if ($materialId = $object->getMaterialId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_MATERIALS);
            $action->set($data);
            $action->where->equalTo('material_id', $materialId);
        } else {
            $action = $sql->insert(self::TABLE_MATERIALS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Material data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setMaterialId($generatedId);
        return $object;
    }

    public function deleteMaterialById(int $materialId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_MATERIALS);
        $action->where->equalTo('material_id', $materialId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Material data was not deleted');
        return;
    }

}