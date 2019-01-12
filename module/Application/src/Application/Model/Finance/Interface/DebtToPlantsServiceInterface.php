<?php

namespace Application\Model\Finance;

use DateTime;

/**
 * Интерфейс сервиса получения записей задолженностей перед заводами
 *
 * Interface DebtToPlantsServiceInterface
 * @package Application\Model\Finance
 */
interface DebtToPlantsServiceInterface  {

    /**
     * Возвращает набор записей задолженностей перед заводами
     *
     * @param $companyId
     * @return DebtToPlantsContainerInterface
     */
    public function getRecords($companyId): DebtToPlantsContainerInterface;

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
