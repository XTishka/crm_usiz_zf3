<?php

namespace Transport\Service;

use Application\Service;
use Transport\Domain\RateEntity;
use Transport\Domain\RateValueEntity;
use Transport\Exception;
use Transport\Service\Repository;
use Zend\Db\Sql\Expression;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class RateManager {

    /** @var  Repository\RateDb */
    protected $rateDbRepository;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * RateManager constructor.
     * @param Repository\RateDb $rateDbRepository
     * @param SessionManager $sessionManager
     */
    public function __construct(Repository\RateDb $rateDbRepository, SessionManager $sessionManager) {
        $this->rateDbRepository = $rateDbRepository;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param null $rateType
     * @param array|null $params
     * @return \Zend\Paginator\Paginator
     */
    public function getRatesPaginator($rateType = null, array $params = null) {
        $container = new Container('ratesFilterParams', $this->sessionManager);
        if (is_array($params) && count($params)) {
            foreach ($params as $key => $value) {
                $container->offsetSet($key, $value);
            }
        } else {
            $params = $container->getArrayCopy();
        }
        $paginator = $this->rateDbRepository->fetchRatesPaginator($rateType, $params);
        return $paginator;
    }

    public function getFilterParams() {
        $container = new Container('ratesFilterParams', $this->sessionManager);
        $params = $container->getArrayCopy();
        return $params;
    }

    public function resetFilterParams() {
        $this->sessionManager->getStorage()->clear('ratesFilterParams');
    }

    /**
     * @param null $direction
     * @return array
     */
    public function getRatesValueOptions($direction = null, $enabled = false) {
        $columns = ['rate_id', 'carrier_id', 'rate_type', 'direction', 'price', 'weight'];
        $options = array_map(function ($option) {
            return [
                'attributes' => [
                    'data-carrier'   => $option['carrier_id'],
                    'data-direction' => $option['direction'],
                    'data-type'      => $option['rate_type'],
                    'data-weight'    => $option['weight'],
                ],
                'label'      => sprintf('%s [%s] - %s - %s', $option['station_name'], $option['weight'], $option['rate_type'], $option['price']),
                'value'      => $option['rate_id'],
            ];
        }, $this->rateDbRepository->fetchRatesArray($direction, $columns, $enabled));
        return $options;
    }

    public function getRatesByParams(array $params = null) {
        $options = array_map(function ($data) {
            $periodBegin = new \DateTime($data['period_begin']);
            $periodEnd = new \DateTime($data['period_end']);
            return [
                'attributes' => [
                    'data-carrier'   => $data['carrier_id'],
                    'data-direction' => $data['direction'],
                    'data-type'      => $data['rate_type'],
                    'data-station'   => $data['station_id'],
                ],
                'label'      => sprintf('%s [%s-%s]', $data['rate_type'], $periodBegin->format('d.m.Y'), $periodEnd->format('d.m.Y')),
                'value'      => $data['rate_id'],
            ];
        }, $this->rateDbRepository->fetchRatesByParams($params));
        return $options;
    }

    public function getValuesByRateId($rateId) {
        $options = array_map(function ($data) {
            $weightBetween = explode('-', $data['weight']);
            return [
                'attributes' => [
                    'data-rate' => $data['rate_id'],
                    'data-min'  => key_exists(0, $weightBetween) ? $weightBetween[0] : 0,
                    'data-max'  => key_exists(1, $weightBetween) ? $weightBetween[1] : 0,
                ],
                'label'      => 0 < $data['min_weight'] ?
                    sprintf('%s %s грн. (min: %s)', $data['weight'], number_format($data['price'], 2), $data['min_weight']) :
                    sprintf('%s %s грн.', $data['weight'], number_format($data['price'], 2)),
                'value'      => $data['value_id'],
            ];
        }, $this->rateDbRepository->fetchValuesByRateId($rateId));
        return $options;
    }


    public function getRatesValueOptionsByParams($params) {
        $options = array_map(function ($option) {
            $data = [
                'attributes' => [
                    'data-carrier'   => $option['carrier_id'],
                    'data-direction' => $option['direction'],
                    'data-type'      => $option['rate_type'],
                    //'data-weight'    => $option['weight'],
                ],
                'label'      => sprintf('%s [%s]', $option['station_name'], $option['rate_type']),
                'options'    => [],
            ];
            $json = preg_replace(['/\\\"/', '/\["/', '/"\]/'], ['"', '[', ']'], $option['values']);

            $values = json_decode($json);

            foreach ($values as $value) {
                $data['options'][$value->value_id] = [
                    'attributes' => [
                        'data-carrier'   => $option['carrier_id'],
                        'data-direction' => $option['direction'],
                        'data-type'      => $option['rate_type'],
                        'data-weight'    => $value->weight,
                    ],
                    'label'      => $option['min_weight'] ?
                        sprintf('%s %s грн. (min: %s)', $value->weight, number_format($value->price, 2), $option['min_weight']) :
                        sprintf('%s %s грн.', $value->weight, number_format($value->price, 2)),
                    'value'      => $value->value_id,
                ];
            }

            return $data;
        }, $this->rateDbRepository->fetchRateValueOptionsByParams($params));

        return $options;
    }

    /**
     * @param $rateId
     * @return RateEntity
     */
    public function getRateById($rateId) {
        $rateId = intval($rateId);
        $rate = $this->rateDbRepository->fetchRateById($rateId);
        return $rate;
    }

    /**
     * @param $rateValueId
     * @return RateValueEntity
     */
    public function getRateValueById($rateValueId) {
        $rateValueId = intval($rateValueId);
        $value = $this->rateDbRepository->fetchRateValueById($rateValueId);
        return $value;
    }

    /**
     * @param RateEntity $object
     * @return Service\Result
     */
    public function saveRate(RateEntity $object) {
        try {
            $object = $this->rateDbRepository->saveRate($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Rate data was successfully saved', $object);
    }

    /**
     * @param $rateId
     * @return Service\Result
     */
    public function deleteRateById($rateId) {
        try {
            $rateId = intval($rateId);
            $object = $this->getRateById($rateId);
            $object->setIsDeleted(true);
            $this->saveRate($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The rate data has been deleted');
    }

}