<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения предоплат поставщикам
 *
 * Interface TotalReceivablesServiceInterface
 * @package Application\Model\Finance
 */
interface TotalReceivablesServiceInterface  {

    /**
     * Возвращает набор записей предоплат поставщикам
     *
     * @param $companyId
     * @return TotalReceivablesContainerInterface
     */
    public function getRecords($companyId): TotalReceivablesContainerInterface;

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
