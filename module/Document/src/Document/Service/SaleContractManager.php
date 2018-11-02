<?php

namespace Document\Service;

use Document\Domain\SaleContractEntity;
use Document\Exception;
use Application\Service;

class SaleContractManager {

    /** @var  Repository\SaleContractDb */
    protected $saleContractDbRepository;

    /**
     * SaleContractManager constructor.
     * @param Repository\SaleContractDb $saleContractDbRepository
     */
    public function __construct(Repository\SaleContractDb $saleContractDbRepository) {
        $this->saleContractDbRepository = $saleContractDbRepository;
    }

    /**
     * @param null $companyId
     * @return \Zend\Paginator\Paginator
     */
    public function getContractsPaginator($companyId = null) {
        $paginator = $this->saleContractDbRepository->fetchContractsPaginator($companyId);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getContractsValueOptions() {
        $columns = ['contract_id', 'contract_number'];
        $options = array_map(function ($saleContract) {
            return [
                //'attributes' => [],
                'label' => $saleContract['contract_number'],
                'value' => $saleContract['contract_id'],
            ];
        }, $this->saleContractDbRepository->fetchContractsArray($columns));
        return $options;
    }

    /**
     * @param $contractId
     * @return SaleContractEntity
     */
    public function getContractById($contractId) {
        $contractId = intval($contractId);
        $saleContract = $this->saleContractDbRepository->fetchContractById($contractId);
        return $saleContract;
    }

    /**
     * @param SaleContractEntity $object
     * @return Service\Result
     */
    public function saveContract(SaleContractEntity $object) {
        try {
            if (!$object->getConsigneeId()) {
                $object->setConsigneeId($object->getContractId());
            }

            $object = $this->saleContractDbRepository->saveContract($object);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Contract data was successfully saved', $object);
    }

    /**
     * @param $contractId
     * @return Service\Result
     */
    public function deleteContractById($contractId) {
        try {
            $contractId = intval($contractId);
            $this->saleContractDbRepository->deleteContractById($contractId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Contract data has been deleted');
    }

}