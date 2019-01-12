<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения предоплаты перевозчикам
 *
 * Interface PrepayToCarriersServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayToCarriersServiceInterface {

    /**
     * Возвращает набор записей предоплаты перевозчикам
     *
     * @param $companyId
     * @return PrepayToCarriersContainerInterface
     */
    public function getRecords($companyId): PrepayToCarriersContainerInterface;

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
