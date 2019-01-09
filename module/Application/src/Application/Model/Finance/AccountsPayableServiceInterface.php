<?php

namespace Application\Model\Finance;

use DateTime;

/**
 * Интерфейс сервиса получения записей общей кредиторской задолженности
 *
 * Interface AccountsPayableServiceInterface
 * @package Application\Model\Finance
 */
interface AccountsPayableServiceInterface {

    /**
     * Возвращает набор записей задолженностей перед прочими контрагентами
     *
     * @param $companyId
     * @return AccountsPayableContainerInterface
     */
    public function getRecords($companyId): AccountsPayableContainerInterface;

    /**
     * Возвращает дату относительно которой фильтруются записи
     * @return \DateTime
     */
    public function getDate(): DateTime;

    /**
     * Устанавливает дату относительно которой фильтруются записи
     * @param DateTime $dateTime
     */
    public function setDate(DateTime $dateTime): void;

}
