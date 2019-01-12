<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса предоплаты от прочих контрагентов
 *
 * Interface PrepayToOthersServiceInterface
 * @package Application\Model\Finance
 */
interface PrepayToOthersServiceInterface {

    /**
     * Возвращает набор записей предоплаты прочим контрагентам
     *
     * @param $companyId
     * @return PrepayToOthersContainerInterface
     */
    public function getRecords($companyId): PrepayToOthersContainerInterface;

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
