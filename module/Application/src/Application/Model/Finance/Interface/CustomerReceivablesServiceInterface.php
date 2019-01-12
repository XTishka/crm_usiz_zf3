<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

/**
 * Интерфейс сервиса получения задолженности покупателей
 *
 * Interface CustomerReceivableServiceInterface
 * @package Application\Model\Finance
 */
interface CustomerReceivableServiceInterface {

    /**
     * Возвращает набор записей задолженностей покупателей
     *
     * @param $companyId
     * @return CustomerReceivableContainerInterface
     */
    public function getRecords($companyId): CustomerReceivableContainerInterface;

}