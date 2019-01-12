<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения предоплаты заводам
 *
 * Interface PrepayToPlantsServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayToPlantsServiceInterface {

    /**
     * Возвращает набор записей предоплат заводам
     *
     * @param $companyId
     * @return PrepayToPlantsContainerInterface
     */
    public function getRecords($companyId): PrepayToPlantsContainerInterface;

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
