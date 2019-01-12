<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

/**
 * Интерфейс сервиса получения задолженности покупателей
 *
 * Interface CustomerReceivablesServiceInterface
 * @package Application\Model\Finance
 */
interface CustomerReceivablesServiceInterface {

    /**
     * Возвращает набор записей задолженностей покупателей
     *
     * @param $companyId
     * @return CustomerReceivablesContainerInterface
     */
    public function getRecords($companyId): CustomerReceivablesContainerInterface;

}