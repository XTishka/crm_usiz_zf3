<?php

namespace Application\Model\Finance;


/**
 * Интерфейс сервиса получения записей уставного фонда
 *
 * Interface CharterCapitalServiceInterface
 * @package Application\Model\Finance
 */
interface CharterCapitalServiceInterface  {

    /**
     * Возвращает набор записей уставного фонда
     *
     * @param $companyId
     * @return CharterCapitalContainerInterface
     */
    public function getRecords($companyId): CharterCapitalContainerInterface;

}
