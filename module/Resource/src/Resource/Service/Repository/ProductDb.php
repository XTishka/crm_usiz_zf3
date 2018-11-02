<?php

namespace Resource\Service\Repository;

use Application\Service\Repository\AbstractDb;
use Resource\Exception;
use Resource\Domain\ProductEntity;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Metadata\Source\Factory as MetadataSourceFactory;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Paginator;

class ProductDb extends AbstractDb {

    const TABLE_PRODUCTS = 'products';

    public function fetchTableColumns() {
        $metadata = MetadataSourceFactory::createSourceFromAdapter($this->dbAdapter);
        $columnNames = $metadata->getColumnNames(self::TABLE_PRODUCTS);
        return $columnNames;
    }

    public function fetchProductsPaginator($productType = null, $sortColumn = 'product_name', $sortDirection = 'ASC') {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_PRODUCTS);
        $select->order(sprintf('%s %s', $sortColumn, $sortDirection));
        if ($productType = trim($productType))
            $select->where->equalTo('product_type', $productType);
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $paginator = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    public function fetchProductsArray($productType = null, $columns = null) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_PRODUCTS);
        if ($productType = trim($productType))
            $select->where->equalTo('product_type', $productType);
        if (is_array($columns))
            $select->columns($columns);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new ResultSet('array');
        $resultSet->initialize($dataSource);
        return $resultSet->toArray();
    }

    public function fetchProductById(int $productId) {
        $sql = new Sql($this->dbAdapter);
        $select = $sql->select(self::TABLE_PRODUCTS);
        $select->where->equalTo('product_id', $productId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new HydratingResultSet($this->hydrator, $this->prototype);
        $resultSet->initialize($dataSource);
        /** @var ProductEntity $object */
        $object = $resultSet->current();
        return $object;
    }

    public function saveProduct(ProductEntity $object) {
        $data = $this->getHydrator()->extract($object);
        unset($data['product_id']);
        $sql = new Sql($this->dbAdapter);
        if ($productId = $object->getProductId()) {
            unset($data['created']);
            $action = $sql->update(self::TABLE_PRODUCTS);
            $action->set($data);
            $action->where->equalTo('product_id', $productId);
        } else {
            $action = $sql->insert(self::TABLE_PRODUCTS);
            $action->values($data);
        }
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Product data was not saved');
        if ($generatedId = $dataSource->getGeneratedValue())
            $object->setProductId($generatedId);
        return $object;
    }

    public function deleteProductById(int $productId) {
        $sql = new Sql($this->dbAdapter);
        $action = $sql->delete(self::TABLE_PRODUCTS);
        $action->where->equalTo('product_id', $productId);
        $dataSource = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$dataSource instanceof ResultInterface || !$dataSource->getAffectedRows())
            throw new Exception\ErrorException('Product data was not deleted');
        return;
    }

}