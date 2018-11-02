<?php

namespace Resource\Service;

use Application\Service;
use Resource\Domain\ProductEntity;
use Resource\Exception;
use Resource\Service\Repository;

class ProductManager {

    /** @var  Repository\ProductDb */
    protected $productDbRepository;

    /**
     * ProductManager constructor.
     * @param Repository\ProductDb $productDbRepository
     */
    public function __construct(Repository\ProductDb $productDbRepository) {
        $this->productDbRepository = $productDbRepository;
    }

    /**
     * @param null $productType
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getProductsPaginator($productType = null, $sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->productDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'product_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->productDbRepository->fetchProductsPaginator($productType, $sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @param null $productType
     * @return array
     */
    public function getProductsValueOptions($productType = null) {
        $columns = ['product_id', 'product_name'];
        $options = array_map(function ($product) {
            return [
                //'attributes' => [],
                'label'      => $product['product_name'],
                'value'      => $product['product_id'],
            ];
        }, $this->productDbRepository->fetchProductsArray($productType, $columns));
        return $options;
    }

    /**
     * @param $productId
     * @return ProductEntity
     */
    public function getProductById($productId) {
        $productId = intval($productId);
        $product = $this->productDbRepository->fetchProductById($productId);
        return $product;
    }

    /**
     * @param ProductEntity $object
     * @return Service\Result
     */
    public function saveProduct(ProductEntity $object) {
        try {
            $object = $this->productDbRepository->saveProduct($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Product data was successfully saved', $object);
    }

    /**
     * @param $productId
     * @return Service\Result
     */
    public function deleteProductById($productId) {
        try {
            $productId = intval($productId);
            $this->productDbRepository->deleteProductById($productId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The product data has been deleted');
    }

}