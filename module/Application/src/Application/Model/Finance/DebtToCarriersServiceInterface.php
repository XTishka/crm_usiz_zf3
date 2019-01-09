<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения записей задолженностей перед перевозчиками
 *
 * Interface DebtToCarriersServiceInterface
 * @package Application\Model\Finance
 */
interface DebtToCarriersServiceInterface {

    /**
     * Возвращает набор записей задолженностей перед перевозчиками
     *
     * @param $companyId
     * @return DebtToCarriersContainerInterface
     */
    public function getRecords($companyId): DebtToCarriersContainerInterface;

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
