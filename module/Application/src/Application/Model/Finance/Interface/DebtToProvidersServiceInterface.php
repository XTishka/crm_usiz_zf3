<?php

namespace Application\Model\Finance;

use DateTime;

/**
 * Интерфейс сервиса получения записей задолженностей перед поставщиками
 *
 * Interface DebtToProvidersServiceInterface
 * @package Application\Model\Finance
 */
interface DebtToProvidersServiceInterface  {

    /**
     * Возвращает набор записей задолженностей перед поставщиками
     *
     * @param $companyId
     * @return DebtToProvidersContainerInterface
     */
    public function getRecords($companyId): DebtToProvidersContainerInterface;

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
