<?php

namespace Document\Service;

use Document\Domain\PurchaseContractEntity;
use Document\Exception;
use Application\Service;

class PurchaseContractManager {

    /** @var  Repository\PurchaseContractDb */
    protected $purchaseContractDbRepository;

    /**
     * PurchaseContractManager constructor.
     * @param Repository\PurchaseContractDb $purchaseContractDbRepository
     */
    public function __construct(Repository\PurchaseContractDb $purchaseContractDbRepository) {
        $this->purchaseContractDbRepository = $purchaseContractDbRepository;
    }

    /**
     * @param null $companyId
     * @return \Zend\Paginator\Paginator
     */
    public function getContractsPaginator($companyId = null) {
        $paginator = $this->purchaseContractDbRepository->fetchContractsPaginator($companyId);
        return $paginator;
    }

    /**
     * @return array
     */
    public function getContractsValueOptions() {
        $columns = ['contract_id', 'contract_number'];
        $options = array_map(function ($purchaseContract) {
            return [
                //'attributes' => [],
                'label' => $purchaseContract['contract_number'],
                'value' => $purchaseContract['contract_id'],
            ];
        }, $this->purchaseContractDbRepository->fetchContractsArray($columns));
        return $options;
    }

    /**
     * @param $contractId
     * @return PurchaseContractEntity
     */
    public function getContractById($contractId) {
        $contractId = intval($contractId);
        $purchaseContract = $this->purchaseContractDbRepository->fetchContractById($contractId);
        return $purchaseContract;
    }

    /**
     * @param PurchaseContractEntity $object
     * @return Service\Result
     */
    public function saveContract(PurchaseContractEntity $object) {
        try {
            $object = $this->purchaseContractDbRepository->saveContract($object);
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
            $this->purchaseContractDbRepository->deleteContractById($contractId);
        } catch (Exception\ErrorException $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Contract data has been deleted');
    }

}