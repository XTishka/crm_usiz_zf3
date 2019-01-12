<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения предоплат поставщикам
 *
 * Interface PrepayToProvidersServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayToProvidersServiceInterface {

    /**
     * Возвращает набор записей предоплат поставщикам
     *
     * @param $companyId
     * @return PrepayToProvidersContainerInterface
     */
    public function getRecords($companyId): PrepayToProvidersContainerInterface;

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
