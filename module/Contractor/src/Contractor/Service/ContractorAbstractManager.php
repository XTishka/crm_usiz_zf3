<?php

namespace Contractor\Service;

use Application\Service\Result;
use Contractor\Entity;
use Contractor\Exception\ErrorException;

abstract class ContractorAbstractManager {

    /**
     * @var Repository\DatabaseContractorAbstract
     */
    protected $databaseContractorAbstractRepository;

    /**
     * ContractorAbstractManager constructor.
     * @param Repository\DatabaseContractorAbstract $databaseContractorAbstractRepository
     */
    public function __construct(Repository\DatabaseContractorAbstract $databaseContractorAbstractRepository) {
        $this->databaseContractorAbstractRepository = $databaseContractorAbstractRepository;
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    public function getContractorsWithPaginator() {
        return $this->databaseContractorAbstractRepository->fetchContractorsWithPaginator();
    }

    /**
     * @return \Zend\Paginator\Paginator
     */
    public function getAllContractorsWithPaginator($sortColumn = null, $sortDirection = null, $query = null) {
        $columnsNames = $this->databaseContractorAbstractRepository->fetchTableColumns();
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'contractor_name';
        if (!in_array($sortColumn, $columnsNames))
            $sortColumn = 'contractor_type';
        $sortDirection = strtoupper($sortDirection);
        if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC')
            $sortDirection = 'ASC';
        $paginator = $this->databaseContractorAbstractRepository->fetchAllContractorsWithPaginator($sortColumn, $sortDirection, $query);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getContractorsValueOptions() {
        $options = $this->databaseContractorAbstractRepository->fetchContractorsValueOptions();
        return $options;
    }

    /**
     * @param $contractorId
     * @return Entity\ContractorAbstract
     * @throws ErrorException
     */
    public function getContractorById($contractorId) {
        $contractorId = intval($contractorId);
        return $this->databaseContractorAbstractRepository->fetchContractorById($contractorId);
    }

    /**
     * @param Entity\ContractorAbstract $object
     * @return Result
     */
    public function saveContractor(Entity\ContractorAbstract $object) {
        try {
            $object = $this->databaseContractorAbstractRepository->saveContractor($object);
        } catch (ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Contractor data was successfully saved.', $object);
    }

    /**
     * @param $contractorId
     * @return Result
     */
    public function deleteContractorById($contractorId) {
        $contractorId = intval($contractorId);
        try {
            $this->databaseContractorAbstractRepository->deleteContractorById($contractorId);
        } catch (ErrorException $exception) {
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Contractor data was successfully deleted.');
    }

}