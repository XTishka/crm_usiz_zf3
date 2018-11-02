<?php

namespace Resource\Service;

use Application\Service;
use Resource\Domain\MaterialEntity;
use Resource\Exception;
use Resource\Service\Repository;

class MaterialManager {

    /** @var  Repository\MaterialDb */
    protected $materialDbRepository;

    /**
     * MaterialManager constructor.
     * @param Repository\MaterialDb $materialDbRepository
     */
    public function __construct(Repository\MaterialDb $materialDbRepository) {
        $this->materialDbRepository = $materialDbRepository;
    }

    /**
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getMaterialsPaginator($sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->materialDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'material_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->materialDbRepository->fetchMaterialsPaginator($sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getMaterialsValueOptions() {
        $columns = ['material_id', 'material_name'];
        $options = array_map(function ($material) {
            return [
                //'attributes' => [],
                'label'      => $material['material_name'],
                'value'      => $material['material_id'],
            ];
        }, $this->materialDbRepository->fetchMaterialsArray($columns));
        return $options;
    }

    /**
     * @param $materialId
     * @return MaterialEntity
     */
    public function getMaterialById($materialId) {
        $materialId = intval($materialId);
        $material = $this->materialDbRepository->fetchMaterialById($materialId);
        return $material;
    }

    /**
     * @param MaterialEntity $object
     * @return Service\Result
     */
    public function saveMaterial(MaterialEntity $object) {
        try {
            $object = $this->materialDbRepository->saveMaterial($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Material data was successfully saved', $object);
    }

    /**
     * @param $materialId
     * @return Service\Result
     */
    public function deleteMaterialById($materialId) {
        try {
            $materialId = intval($materialId);
            $this->materialDbRepository->deleteMaterialById($materialId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The material data has been deleted');
    }

}