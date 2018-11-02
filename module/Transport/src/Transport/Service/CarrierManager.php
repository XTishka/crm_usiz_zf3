<?php

namespace Transport\Service;

use Application\Service;
use Transport\Domain\CarrierEntity;
use Transport\Exception;
use Transport\Service\Repository;

class CarrierManager {

    /** @var  Repository\CarrierDb */
    protected $carrierDbRepository;

    /**
     * CarrierManager constructor.
     * @param Repository\CarrierDb $carrierDbRepository
     */
    public function __construct(Repository\CarrierDb $carrierDbRepository) {
        $this->carrierDbRepository = $carrierDbRepository;
    }

    /**
     * @param null $carrierType
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getCarriersPaginator($carrierType = null, $sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->carrierDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'carrier_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->carrierDbRepository->fetchCarriersPaginator($carrierType, $sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @param null $carrierType
     * @return array
     */
    public function getCarriersValueOptions($carrierType = null) {
        $columns = ['carrier_id', 'carrier_type', 'carrier_name', 'country'];
        $options = array_map(function ($carrier) {
            return [
                'attributes' => [
                    'data-type'    => $carrier['carrier_type'],
                    'data-country' => $carrier['country'],
                ],
                'label'      => sprintf('%s (%s)', $carrier['carrier_name'], $carrier['carrier_type']),
                'value'      => $carrier['carrier_id'],
            ];
        }, $this->carrierDbRepository->fetchCarriersArray($carrierType, $columns));
        return $options;
    }

    public function getCarrierValueOptionsByParams(array $params = null) {
        $options = array_map(function ($carrier) {
            return [
                'attributes' => [
                    'data-type'    => $carrier['carrier_type'],
                    'data-country' => $carrier['country'],
                ],
                'label'      => $carrier['carrier_name'],
                'value'      => $carrier['carrier_id'],
            ];
        }, $this->carrierDbRepository->fetchCarrierValueOptionsByParams($params));
        return $options;
    }

    /**
     * @param $carrierId
     * @return CarrierEntity
     */
    public function getCarrierById($carrierId) {
        $carrierId = intval($carrierId);
        $carrier = $this->carrierDbRepository->fetchCarrierById($carrierId);
        return $carrier;
    }

    /**
     * @param CarrierEntity $object
     * @return Service\Result
     */
    public function saveCarrier(CarrierEntity $object) {
        try {
            $object = $this->carrierDbRepository->saveCarrier($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Carrier data was successfully saved', $object);
    }

    /**
     * @param $carrierId
     * @return Service\Result
     */
    public function deleteCarrierById($carrierId) {
        try {
            $carrierId = intval($carrierId);
            $this->carrierDbRepository->deleteCarrierById($carrierId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The carrier data has been deleted');
    }

}