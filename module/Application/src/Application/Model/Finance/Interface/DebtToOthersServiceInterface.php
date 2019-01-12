<?php

namespace Application\Model\Finance;

use DateTime;


/**
 * Интерфейс сервиса получения записей задолженностей перед прочими контрагентами
 *
 * Interface DebtToOthersServiceInterface
 * @package Application\Model\Finance
 */
interface DebtToOthersServiceInterface {

    /**
     * Возвращает набор записей задолженностей перед прочими контрагентами
     *
     * @param $companyId
     * @return DebtToOthersContainerInterface
     */
    public function getRecords($companyId): DebtToOthersContainerInterface;

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
