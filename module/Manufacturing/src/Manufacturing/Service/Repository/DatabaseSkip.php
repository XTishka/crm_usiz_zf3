<?php

namespace Manufacturing\Service\Repository;

use Application\Service\Repository\AbstractDb;
use DateTime;
use Manufacturing\Domain\SkipCommonEntity;
use Manufacturing\Domain\SkipMaterialEntity;
use Manufacturing\Exception\ErrorException;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Combine;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;

class DatabaseSkip extends AbstractDb {

    const TABLE_SKIP_COMMON    = 'skip_common';
    const TABLE_SKIP_MATERIALS = 'skip_materials';

    public function fetchSkipLogsArray($furnaceId, DateTime $date = null) {
        $date = ($date instanceof DateTime) ? $date : new DateTime();
        $sql = new Sql($this->dbAdapter);

        /*
        $select = $sql->select(['a' => self::TABLE_SKIP_COMMON]);
        $select->columns(['skip_id', 'date']);
        $select->join(['b' => self::TABLE_SKIP_MATERIALS], 'a.skip_id = b.skip_id', ['weight_material' => new Expression('SUM(b.weight)'), 'product_weight', 'dropout_weight']);
        $select->join(['c' => self::TABLE_SKIP_MATERIALS], 'a.skip_id = c.skip_id', ['weight_coal' => new Expression('SUM(c.weight)')]);
        //$select->join(['c' => self::TABLE_SKIP_MATERIALS], 'a.skip_id = c.skip_id', ['weight_coal' => new Expression('c.weight')]);
        $select->where->equalTo('a.furnace_id', $furnaceId);
        $select->where->greaterThanOrEqualTo('date', $date->format('Y-m-d'));
        $select->where->notEqualTo('b.dropout', 0);
        $select->where->equalTo('c.dropout', 0);
        $select->group(['date']);
        $select->group(['skip_id']);
        $select->order('date DESC');
        */

        $selectA = $sql->select(['a' => self::TABLE_SKIP_COMMON]);
        $selectA->columns(['skip_id', 'date']);
        $selectA->join(['b' => self::TABLE_SKIP_MATERIALS], 'a.skip_id = b.skip_id', ['weight_material' => new Expression('SUM(b.weight)'), 'product_weight', 'dropout_weight']);
        $selectA->where->equalTo('a.furnace_id', $furnaceId);
        $selectA->where->greaterThanOrEqualTo('date', $date->format('Y-m-d'));
        $selectA->where->notEqualTo('b.dropout', 0);
        $selectA->group('a.skip_id');

        $selectB = $sql->select(['a' => self::TABLE_SKIP_COMMON]);
        $selectB->columns(['skip_id', 'date']);
        $selectB->join(['b' => self::TABLE_SKIP_MATERIALS], 'a.skip_id = b.skip_id', [
            'weight_coal' => new Expression('SUM(b.weight)')
        ]);
        $selectB->where->equalTo('a.furnace_id', $furnaceId);
        $selectB->where->greaterThanOrEqualTo('date', $date->format('Y-m-d'));
        $selectB->where->equalTo('b.dropout', 0);
        $selectB->group('a.skip_id');

        $select = $sql->select(['q1' => $selectA]);
        $select->join(['q2' => $selectB], 'q1.skip_id = q2.skip_id', ['weight_coal']);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
        $resultSet->initialize($dataSource);
        $data = $resultSet->toArray();

        //echo '<pre>';print_r($data);echo '</pre>';

        return $data;
    }

    /**
     * @param int $skipId
     * @return SkipCommonEntity
     * @throws ErrorException
     */
    public function fetchOneSkipById(int $skipId) {
        $sql = new Sql($this->dbAdapter);
        $selectCommon = $sql->select(self::TABLE_SKIP_COMMON);
        $selectCommon->where->equalTo('skip_id', $skipId);

        $dataSourceCommon = $sql->prepareStatementForSqlObject($selectCommon)->execute();

        if (!$dataSourceCommon instanceof ResultInterface || !$dataSourceCommon->count())
            throw new ErrorException('Skip data was not found.');

        $resultSetCommon = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSetCommon->initialize($dataSourceCommon);

        /** @var SkipCommonEntity $object */
        $object = $resultSetCommon->current();

        $selectMaterial = $sql->select(self::TABLE_SKIP_MATERIALS);
        $selectMaterial->where->equalTo('skip_id', $skipId);

        $dataSourceMaterial = $sql->prepareStatementForSqlObject($selectMaterial)->execute();

        if (!$dataSourceMaterial instanceof ResultInterface || !$dataSourceMaterial->count())
            throw new ErrorException('Skip material data was not found.');

        $resultSetMaterial = new HydratingResultSet($this->hydrator, new SkipMaterialEntity());
        $resultSetMaterial->initialize($dataSourceMaterial);

        $materialStack = [];
        foreach ($resultSetMaterial as $material) {
            array_push($materialStack, $material);
        }

        $object->setMaterials($materialStack);

        return $object;
    }

    /**
     * @param SkipCommonEntity $object
     * @return SkipCommonEntity
     * @throws ErrorException
     */
    public function saveSkip(SkipCommonEntity $object) {
        $data = $this->hydrator->extract($object);

        unset($data['skip_id'], $data['materials']);
        $sql = new Sql($this->dbAdapter);
        if ($skipId = $object->getSkipId()) {
            $action = $sql->update(self::TABLE_SKIP_COMMON);
            $action->set($data);
            $action->where->equalTo('skip_id', $skipId);
        } else {
            $action = $sql->insert(self::TABLE_SKIP_COMMON);
            $action->values($data);
        }

        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();

        if (!$dataSource instanceof ResultInterface)
            throw new ErrorException('Skip data was not saved.');

        if ($generatedId = $dataSource->getGeneratedValue()) {
            $object->setSkipId($generatedId);
        } else {
            $delete = $sql->delete(self::TABLE_SKIP_MATERIALS);
            $delete->where->equalTo('skip_id', $object->getSkipId());
            $dataSource = $sql->prepareStatementForSqlObject($delete)->execute();
            if (!$dataSource instanceof ResultInterface)
                throw new ErrorException('Skip materials data was not deleted.');
        }

        /** @var SkipMaterialEntity $material */
        foreach ($object->getMaterials() as $material) {
            if ($material->getDropout()) {
                $dropoutWeight = ((float)$material->getWeight() * 100) / (100 - $material->getDropout()) - $material->getWeight();
                $material->setDropoutWeight($dropoutWeight);
                $productWeight = 0.58 * $material->getWeight();
                $material->setProductWeight($productWeight);
            }
            $values = $this->hydrator->extract($material);
            $values['skip_id'] = $object->getSkipId();
            $insert = $sql->insert(self::TABLE_SKIP_MATERIALS);
            $insert->values($values);
            $dataSource = $sql->prepareStatementForSqlObject($insert)->execute();
            if (!$dataSource instanceof ResultInterface)
                throw new ErrorException('Skip material data was not saved.');
        }

        return $object;
    }

    /**
     * @param $skipId
     * @throws ErrorException
     */
    public function deleteSkip($skipId) {
        $sql = new Sql($this->dbAdapter);

        $action = $sql->delete(self::TABLE_SKIP_MATERIALS);
        $action->where->equalTo('skip_id', $skipId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new ErrorException('Skip material transaction was not saved.');

        $action = $sql->delete(self::TABLE_SKIP_COMMON);
        $action->where->equalTo('skip_id', $skipId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface)
            throw new ErrorException('Skip common transaction was not saved.');
    }

}