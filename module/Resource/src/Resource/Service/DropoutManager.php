<?php

namespace Resource\Service;

use Contractor\Entity\ContractorProvider;
use Contractor\Service\Repository\DatabaseContractorProvider;
use Resource\Domain\DropoutEntity;
use Resource\Exception\DeleteErrorException;
use Resource\Exception\NotFoundException;
use Resource\Exception\SaveErrorException;
use Resource\Service\Repository\MaterialDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class DropoutManager {

    const TABLE_DROPOUTS = 'dropouts';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * DropoutManager constructor.
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
        $select = $sql->select(['a' => self::TABLE_DROPOUTS]);
        $select->join(['b' => DatabaseContractorProvider::TABLE_CONTRACTORS], 'a.provider_id = b.contractor_id', ['provider_name' => 'contractor_name']);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'a.material_id = c.material_id', ['material_name' => 'material_name']);
        $select->order(['period_begin ASC', 'period_end ASC']);
        $select->where->equalTo('b.contractor_type', ContractorProvider::TYPE_PROVIDER);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. Dropout data not received');
        $resultSet = new HydratingResultSet($this->hydrator, new DropoutEntity());
        $paginator = new Paginator(new DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    /**
     * @param $dropoutId
     * @return DropoutEntity
     * @throws NotFoundException
     */
    public function fetchOneById($dropoutId) {
        $dropoutId = intval($dropoutId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(['a' => self::TABLE_DROPOUTS]);
        $select->join(['b' => DatabaseContractorProvider::TABLE_CONTRACTORS], 'a.provider_id = b.contractor_id', ['provider_name' => 'contractor_name']);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'a.material_id = c.material_id', ['material_name' => 'material_name']);
        $select->where->equalTo('a.dropout_id', $dropoutId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult() || !$dataSource->count())
            throw new NotFoundException('No dropouts found');
        $resultSet = new HydratingResultSet($this->hydrator, new DropoutEntity());
        $resultSet->initialize($dataSource);
        /** @var DropoutEntity $dropoutEntity */
        $dropoutEntity = $resultSet->current();
        return $dropoutEntity;
    }

    /**
     * @param $providerId
     * @return DropoutEntity
     * @throws NotFoundException
     */
    public function fetchOneByProviderId($providerId) {
        $providerId = intval($providerId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(['a' => self::TABLE_DROPOUTS]);
        $select->join(['b' => DatabaseContractorProvider::TABLE_CONTRACTORS], 'a.provider_id = b.contractor_id', ['provider_name' => 'contractor_name']);
        $select->join(['c' => MaterialDb::TABLE_MATERIALS], 'a.material_id = c.material_id', ['material_name' => 'material_name']);
        $select->where->equalTo('a.provider_id', $providerId);
        $select->where->nest()
            ->lessThanOrEqualTo('a.period_begin', (new \DateTime())->format('Y-m-d'))
            ->greaterThanOrEqualTo('a.period_end', (new \DateTime())->format('Y-m-d'));
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult() || !$dataSource->count())
            throw new NotFoundException('No dropouts found');
        $resultSet = new HydratingResultSet($this->hydrator, new DropoutEntity());
        $resultSet->initialize($dataSource);
        /** @var DropoutEntity $dropoutEntity */
        $dropoutEntity = $resultSet->current();
        return $dropoutEntity;
    }

    /**
     * @param DropoutEntity $dropoutEntity
     * @return DropoutEntity
     * @throws SaveErrorException
     */
    public function saveDropout(DropoutEntity $dropoutEntity) {
        $data = $this->hydrator->extract($dropoutEntity);
        $sql = new Sql($this->adapter, self::TABLE_DROPOUTS);
        if ($dropoutId = $dropoutEntity->getDropoutId()) {
            $action = $sql->update();
            $action->set($data);
            $action->where->equalTo('dropout_id', $dropoutId);
        } else {
            $action = $sql->insert();
            $action->values($data);
        }
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->getAffectedRows())
            throw new SaveErrorException('Unknown error. The dropout data was not saved');
        if ($dropoutId = $result->getGeneratedValue())
            $dropoutEntity->setDropoutId($dropoutId);
        return $dropoutEntity;
    }


    /**
     * @param int $dropoutId
     * @throws DeleteErrorException
     */
    public function deleteDropout(int $dropoutId) {
        $dropoutId = intval($dropoutId);
        $sql = new Sql($this->adapter, self::TABLE_DROPOUTS);
        $action = $sql->delete();
        $action->where->equalTo('dropout_id', $dropoutId);
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->isQueryResult() || !$result->getAffectedRows())
            throw new DeleteErrorException('Unknown error. The dropout data was not deleted');
        return;
    }

}