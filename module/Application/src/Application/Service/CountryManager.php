<?php

namespace Application\Service;

use Application\Exception;
use Application\Service;
use Application\Domain\CountryEntity;
use Application\Service\Repository;

class CountryManager {

    /** @var  Repository\CountryDb */
    protected $countryDbRepository;

    /**
     * CountryManager constructor.
     * @param Repository\CountryDb $countryDbRepository
     */
    public function __construct(Repository\CountryDb $countryDbRepository) {
        $this->countryDbRepository = $countryDbRepository;
    }

    /**
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getCountriesPaginator($sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->countryDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'country_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->countryDbRepository->fetchCountriesPaginator($sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getCountriesValueOptions() {
        $columns = ['country_id', 'country_name', 'country_code'];
        $options = array_map(function ($option) {
            return [
                'attributes' => [
                    'data-code'  => $option['country_code'],
                    'data-image' => sprintf('/img/flags/%s.png', $option['country_code']),
                ],
                'label'      => $option['country_name'],
                'value'      => $option['country_code'],
            ];
        }, $this->countryDbRepository->fetchCountriesArray($columns));
        return $options;
    }

    /**
     * @param $countryId
     * @return CountryEntity
     */
    public function getCountryById($countryId) {
        $countryId = intval($countryId);
        $country = $this->countryDbRepository->fetchCountryById($countryId);
        return $country;
    }

    /**
     * @param CountryEntity $object
     * @return Service\Result
     */
    public function saveCountry(CountryEntity $object) {
        try {
            $object = $this->countryDbRepository->saveCountry($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Country data was successfully saved', $object);
    }

    /**
     * @param $countryId
     * @return Service\Result
     */
    public function deleteCountryById($countryId) {
        try {
            $countryId = intval($countryId);
            $this->countryDbRepository->deleteCountryById($countryId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The country data has been deleted');
    }

}