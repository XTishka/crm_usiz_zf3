<?php

namespace Transport\Service;

use Application\Service;
use Transport\Exception;
use Transport\Domain\StationEntity;
use Transport\Service\Repository;

class StationManager {

    /** @var  Repository\StationDb */
    protected $stationDbRepository;

    /**
     * StationManager constructor.
     * @param Repository\StationDb $stationDbRepository
     */
    public function __construct(Repository\StationDb $stationDbRepository) {
        $this->stationDbRepository = $stationDbRepository;
    }

    /**
     * @param null $stationType
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getStationsPaginator($stationType = null, $sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->stationDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'station_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->stationDbRepository->fetchStationsPaginator($stationType, $sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @param null $stationType
     * @return array
     */
    public function getStationsValueOptions($stationType = null) {
        $columns = ['station_id', 'station_type', 'station_name', 'country'];
        $options = array_map(function ($station) {
            return [
                'attributes' => [
                    'data-type'    => $station['station_type'],
                    'data-country' => $station['country'],
                ],
                'label'      => $station['station_name'],
                'value'      => $station['station_id'],
            ];
        }, $this->stationDbRepository->fetchStationsArray($stationType, $columns));
        return $options;
    }

    /**
     * @param $stationId
     * @return StationEntity
     */
    public function getStationById($stationId) {
        $stationId = intval($stationId);
        $station = $this->stationDbRepository->fetchStationById($stationId);
        return $station;
    }

    /**
     * @param StationEntity $object
     * @return Service\Result
     */
    public function saveStation(StationEntity $object) {
        try {
            $object = $this->stationDbRepository->saveStation($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Station data was successfully saved', $object);
    }

    /**
     * @param $stationId
     * @return Service\Result
     */
    public function deleteStationById($stationId) {
        try {
            $stationId = intval($stationId);
            $this->stationDbRepository->deleteStationById($stationId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The station data has been deleted');
    }

}