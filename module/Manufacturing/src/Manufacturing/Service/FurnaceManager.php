<?php

namespace Manufacturing\Service;

use Application\Exception;
use Application\Service;
use Manufacturing\Domain\FurnaceEntity;
use Manufacturing\Service\Repository;

class FurnaceManager {

    /** @var  Repository\FurnaceDb */
    protected $furnaceDbRepository;

    /**
     * FurnaceManager constructor.
     * @param Repository\FurnaceDb $furnaceDbRepository
     */
    public function __construct(Repository\FurnaceDb $furnaceDbRepository) {
        $this->furnaceDbRepository = $furnaceDbRepository;
    }

    /**
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getFurnacesPaginator($sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->furnaceDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'furnace_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->furnaceDbRepository->fetchFurnacesPaginator($sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getFurnacesValueOptions() {
        $columns = ['furnace_id', 'plant_id', 'furnace_name'];
        $options = array_map(function ($furnace) {
            return [
                'attributes' => [
                    'data-plant' => $furnace['plant_id'],
                ],
                'label'      => $furnace['furnace_name'],
                'value'      => $furnace['furnace_id'],
            ];
        }, $this->furnaceDbRepository->fetchFurnacesArray($columns));
        return $options;
    }

    /**
     * @param $furnaceId
     * @return FurnaceEntity
     */
    public function getFurnaceById($furnaceId) {
        $furnaceId = intval($furnaceId);
        $furnace = $this->furnaceDbRepository->fetchFurnaceById($furnaceId);
        return $furnace;
    }

    /**
     * @param FurnaceEntity $object
     * @return Service\Result
     */
    public function saveFurnace(FurnaceEntity $object) {
        try {
            $object = $this->furnaceDbRepository->saveFurnace($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Furnace data was successfully saved', $object);
    }

    /**
     * @param $furnaceId
     * @return Service\Result
     */
    public function deleteFurnaceById($furnaceId) {
        try {
            $furnaceId = intval($furnaceId);
            $this->furnaceDbRepository->deleteFurnaceById($furnaceId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The furnace data has been deleted');
    }

}