<?php

namespace Document\Service\Repository;

use Application\Service\Repository;
use Application\Service\Repository\CountryDb;
use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorCompany;
use Document\Domain\SaleContractEntity as ContractEntity;
use Document\Exception;
use Manufacturing\Service\Repository\WarehouseDb;
use Resource\Service\Repository\ProductDb;
use Transport\Service\Repository\StationDb;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class SaleContractDb extends Repository\AbstractDb {

    const TABLE_SALE_CONTRACTS = 'sale_contracts';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_SALE_CONTRACTS);
        return $columnNames;
    }

    public function fetchContractsPaginator($companyId = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_SALE_CONTRACTS]);
        $select->join(['b' => DatabaseContractorCompany::TABLE_CONTRACTORS], 'a.company_id = b.contractor_id', ['company_name' => 'contractor_name']);
        $select->join(['c' => ProductDb::TABLE_PRODUCTS], 'a.product_id = c.product_id', ['product_name']);
        $select->join(['d' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.customer_id = d.contractor_id', ['customer_name' => 'contractor_name']);
        $select->join(['e' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.consignee_id = e.contractor_id', ['consignee_name' => 'contractor_name'], Join::JOIN_LEFT);
        $select->join(['f' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = f.warehouse_id', ['warehouse_name']);
        $select->join(['g' => CountryDb::TABLE_COUNTRIES], 'a.country = g.country_code', ['country_name']);
        $select->join(['w' => SaleWagonDb::TABLE_SALE_WAGONS], new Expression('a.contract_id = w.contract_id'), ['loadedWagons' => new Expression('(COUNT(DISTINCT w.wagon_id))')], Join::JOIN_LEFT);
        $select->where->equalTo('b.contractor_type', ContractorAbstract::TYPE_COMPANY);
        $select->where->equalTo('d.contractor_type', ContractorAbstract::TYPE_CUSTOMER);
        //$select->where->equalTo('e.contractor_type', ContractorAbstract::TYPE_CONSIGNEE);
        $select->order('created DESC');
        $select->group('a.contract_id');
        if ($companyId)
            $select->where->equalTo('a.company_id', $companyId);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchContractsArray($columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_SALE_CONTRACTS);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchContractById(int $contractId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(['a' => self::TABLE_SALE_CONTRACTS]);
        $select->join(['b' => DatabaseContractorCompany::TABLE_CONTRACTORS], 'a.company_id = b.contractor_id', ['company_name' => 'contractor_name']);
        $select->join(['c' => ProductDb::TABLE_PRODUCTS], 'a.product_id = c.product_id', ['product_name']);
        $select->join(['d' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.customer_id = d.contractor_id', ['customer_name' => 'contractor_name']);
        $select->join(['e' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.consignee_id = e.contractor_id', ['consignee_name' => 'contractor_name'], Join::JOIN_LEFT);
        $select->join(['f' => WarehouseDb::TABLE_WAREHOUSES], 'a.warehouse_id = f.warehouse_id', ['warehouse_name']);
        $select->join(['g' => CountryDb::TABLE_COUNTRIES], 'a.country = g.country_code', ['country_name']);
        $select->join(['h' => StationDb::TABLE_STATIONS], 'a.station_id = h.station_id', ['station_name'], Join::JOIN_LEFT);
        $select->where->equalTo('b.contractor_type', ContractorAbstract::TYPE_COMPANY);
        $select->where->equalTo('d.contractor_type', ContractorAbstract::TYPE_CUSTOMER);
        //$select->where->equalTo('e.contractor_type', ContractorAbstract::TYPE_CONSIGNEE);
        $select->where->equalTo('a.contract_id', $contractId);
        $select->order('created DESC');
        $select->group('a.contract_id');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var ContractEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    /**
     * @param ContractEntity $object
     * @return ContractEntity
     * @throws Exception\ErrorException
     */
    public function saveContract(ContractEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['contract_id']);
        $sql = new Sql($this->dbAdapter);
        if ($contractId = $object->getContractId()) {
            $action = $sql->update(self::TABLE_SALE_CONTRACTS);
            $action->set($data);
            $action->where->equalTo('contract_id', $contractId);
        } else {
            $action = $sql->insert(self::TABLE_SALE_CONTRACTS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Contract data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setContractId($generatedId);
        return $object;
    }

    /**
     * @param int $contractId
     * @throws Exception\ErrorException
     */
    public function deleteContractById(int $contractId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_SALE_CONTRACTS);
        $action->where->equalTo('contract_id', $contractId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Contract data was not deleted');
        return;
    }

}