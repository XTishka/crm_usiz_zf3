<?php

namespace Application\Service;

use Application\Exception;
use Application\Service;
use Application\Domain\TaxEntity;
use Application\Service\Repository;
use NumberFormatter;

class TaxManager {

    /** @var  Repository\TaxDb */
    protected $taxDbRepository;

    /**
     * TaxManager constructor.
     * @param Repository\TaxDb $taxDbRepository
     */
    public function __construct(Repository\TaxDb $taxDbRepository) {
        $this->taxDbRepository = $taxDbRepository;
    }

    public function getCurrentTaxValue() {
        return $this->taxDbRepository->fetchCurrentTaxValue();
    }

    /**
     * @param null $sortColumn
     * @param null $sortDirection
     * @return \Zend\Paginator\Paginator
     */
    public function getTaxesPaginator($sortColumn = null, $sortDirection = null) {
        $columnsNames = $this->taxDbRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'tax_name';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->taxDbRepository->fetchTaxesPaginator($sortColumn, $sortDirection);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getTaxesValueOptions() {
        $columns = ['tax_id', 'tax_name', 'value'];
        $options = array_map(function ($option) {
            return [
                'label' => $option['tax_name'],
                'value' => $option['value'],
            ];
        }, $this->taxDbRepository->fetchTaxesArray($columns));
        return $options;
    }

    /**
     * @param $taxId
     * @return TaxEntity
     */
    public function getTaxById($taxId) {
        $taxId = intval($taxId);
        $tax = $this->taxDbRepository->fetchTaxById($taxId);
        return $tax;
    }

    /**
     * @param TaxEntity $object
     * @return Service\Result
     */
    public function saveTax(TaxEntity $object) {
        try {
            $object = $this->taxDbRepository->saveTax($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Tax data was successfully saved', $object);
    }

    /**
     * @param $taxId
     * @return Service\Result
     */
    public function deleteTaxById($taxId) {
        try {
            $taxId = intval($taxId);
            $this->taxDbRepository->deleteTaxById($taxId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'The tax data has been deleted');
    }

    public static function calculate($value, $tax) {
        $value += $value * $tax / 100;
        return $value;
    }

}