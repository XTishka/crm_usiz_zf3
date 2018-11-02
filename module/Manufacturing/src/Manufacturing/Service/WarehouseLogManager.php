<?php

namespace Manufacturing\Service;

use Application\Service\Result;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Exception;
use Manufacturing\Service\Repository;

class WarehouseLogManager {

    /**
     * @var Repository\WarehouseLogDb
     */
    protected $warehouseLogDbRepository;

    /**
     * WarehouseLogManager constructor.
     * @param Repository\WarehouseLogDb $warehouseLogDbRepository
     */
    public function __construct(Repository\WarehouseLogDb $warehouseLogDbRepository) {
        $this->warehouseLogDbRepository = $warehouseLogDbRepository;
    }

    /**
     * @param $plantId
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws Exception\ErrorException
     */
    public function getTotalMaterialBalances($plantId, $date = null) {
        $plantId = intval($plantId);
        return $this->warehouseLogDbRepository->fetchTotalMaterialBalances($plantId, $date);
    }

    /**
     * @param $plantId
     * @return array
     * @throws Exception\ErrorException
     */
    public function getMaterialBalances($plantId) {
        return $this->warehouseLogDbRepository->fetchMaterialBalances($plantId);
    }

    /**
     * @param $plantId
     * @return array
     * @throws Exception\ErrorException
     */
    public function getProductBalances($plantId) {
        return $this->warehouseLogDbRepository->fetchProductBalances($plantId);
    }

    public function getMaterialValueOptions() {
        $options = $this->warehouseLogDbRepository->fetchWarehouseMaterialValueOptions();
        return array_map(function ($option) {
            $fraction = json_decode($option['fraction']);
            return [
                'attributes' => [
                    'data-dropout'  => ($fraction->min_size && $fraction->max_size) ? 'true' : 'false',
                    'data-plant'    => $option['plant_id'],
                    'data-provider' => $option['provider_id'],
                ],
                'label'      => sprintf('%s :: %s тонн', $option['material_name'], $option['weight']),
                'value'      => $option['material_id'],
            ];
        }, $options);
    }

    public function input(WarehouseLogEntity $object) {
        try {
            $object->setDirection($object::DIRECTION_INPUT);
            $object = $this->warehouseLogDbRepository->saveLog($object);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Warehouse transaction successfully saved', $object);
    }

    public function output(WarehouseLogEntity $object) {
        try {
            $object->setDirection($object::DIRECTION_OUTPUT);
            $object = $this->warehouseLogDbRepository->saveLog($object);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Warehouse transaction successfully saved', $object);
    }

    public function deleteLogByWagonId($wagonId) {
        try {
            $this->warehouseLogDbRepository->deleteLogByWagonId($wagonId);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Warehouse transaction successfully deleted.');
    }

    public function deleteLogBySkipId($skipId) {
        try {
            $this->warehouseLogDbRepository->deleteLogBySkipId($skipId);
        } catch (Exception\ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Warehouse transaction successfully deleted.');
    }

}