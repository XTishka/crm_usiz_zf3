<?php

namespace Application\Model\Finance;

use DateTime;

/**
 * Интерфейс сервиса получения предоплат поставщикам
 *
 * Interface PrepayFromCustomersServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayFromCustomersServiceInterface  {

    /**
     * Возвращает набор записей предоплаты от покупателей
     *
     * @param $companyId
     * @return PrepayFromCustomersContainerInterface
     */
    public function getRecords($companyId): PrepayFromCustomersContainerInterface;

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
