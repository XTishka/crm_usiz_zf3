<?php

namespace Application\Service\Repository;

use Application\Exception;
use Application\Domain\CountryEntity;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class CountryDb extends AbstractDb {

    const TABLE_COUNTRIES = 'countries';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_COUNTRIES);
        return $columnNames;
    }

    public function fetchCountriesPaginator($sortColumn = 'country_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_COUNTRIES);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchCountriesArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_COUNTRIES);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchCountryById(int $countryId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_COUNTRIES);
        $select->where->equalTo('country_id', $countryId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var CountryEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveCountry(CountryEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['country_id']);
        $sql = new Sql($this->dbAdapter);
        if ($countryId = $object->getCountryId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_COUNTRIES);
            $action->set($data);
            $action->where->equalTo('country_id', $countryId);
        } else {
            $action = $sql->insert(self::TABLE_COUNTRIES);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Country data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setCountryId($generatedId);
        return $object;
    }

    public function deleteCountryById(int $countryId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_COUNTRIES);
        $action->where->equalTo('country_id', $countryId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Country data was not deleted');
        return;
    }

}