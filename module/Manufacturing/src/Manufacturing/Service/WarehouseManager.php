<?php

namespace Manufacturing\Service;

use Application\Exception;
use Application\Service;
use Manufacturing\Domain\WarehouseEntity;
use Manufacturing\Service\Repository;

class WarehouseManager {

    /** @var  Repository\WarehouseDb */
    protected $warehouseDbRepository;

    /**
     * WarehouseManager constructor.
     * @param Repository\WarehouseDb $warehouseDbRepository
     */
    public function __construct(Repository\WarehouseDb $warehouseDbRepository) {
        $this->warehouseDbRepository = $warehouseDbRepository;
    }

    /**
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getWarehousesPaginator($sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->warehouseDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'warehouse_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->warehouseDbRepository->fetchWarehousesPaginator($sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getWarehousesValueOptions() {
        $columns = ['warehouse_id', 'plant_id', 'warehouse_name', 'warehouse_type'];
        $options = array_map(function ($warehouse) {
            return [
                'attributes' => [
                    'data-plant' => $warehouse['plant_id'],
                    'data-type'  => $warehouse['warehouse_type'],
                ],
                'label'      => $warehouse['warehouse_name'],
                'value'      => $warehouse['warehouse_id'],
            ];
        }, $this->warehouseDbRepository->fetchWarehousesArray($columns));
        return $options;
    }

    /**
     * @param $warehouseId
     * @return WarehouseEntity
     */
    public function getWarehouseById($warehouseId) {
        $warehouseId = intval($warehouseId);
        $warehouse = $this->warehouseDbRepository->fetchWarehouseById($warehouseId);
        return $warehouse;
    }

    /**
     * @param WarehouseEntity $object
     * @return Service\Result
     */
    public function saveWarehouse(WarehouseEntity $object) {
        try {
            $object = $this->warehouseDbRepository->saveWarehouse($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Warehouse data was successfully saved', $object);
    }

    /**
     * @param $warehouseId
     * @return Service\Result
     */
    public function deleteWarehouseById($warehouseId) {
        try {
            $warehouseId = intval($warehouseId);
            $this->warehouseDbRepository->deleteWarehouseById($warehouseId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The warehouse data has been deleted');
    }

}