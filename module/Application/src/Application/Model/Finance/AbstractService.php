<?php
/**
 * @author    Bogdan Tereshchenko <development.sites@gmail.com>
 */

namespace Application\Model\Finance;

use DateTime;
use Zend\Db\Adapter\AdapterInterface;

abstract class AbstractService implements ServiceInterface {

    /**
     * @var AdapterInterface
     */
    protected $db;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * AbstractFinanceService constructor.
     * @param AdapterInterface $db
     */
    public function __construct(AdapterInterface $db) {
        $this->db = $db;
    }

    abstract function getRecords($companyId): ContainerInterface;

    /**
     * Возвращает дату относительно которой фильтруются записи
     * @return DateTime
     * @throws \Exception
     */
    public function getDate(): DateTime {
        if (!$this->date instanceof DateTime)
            $this->date = new DateTime('now');
        return $this->date;
    }

    /**
     * Устанавливает дату относительно которой фильтруются записи
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void {
        $this->date = $date;
    }

}