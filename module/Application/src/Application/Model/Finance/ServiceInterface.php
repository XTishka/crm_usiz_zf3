<?php

namespace Application\Model\Finance;

use DateTime;

/**
 * Интерфейс сервиса получения записей
 *
 * Interface AccountsPayableServiceInterface
 * @package Application\Model\Finance
 */
interface ServiceInterface {

    /**
     * Возвращает набор записей
     * @param $companyId
     * @return ContainerInterface
     */
    public function getRecords($companyId): ContainerInterface;

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
