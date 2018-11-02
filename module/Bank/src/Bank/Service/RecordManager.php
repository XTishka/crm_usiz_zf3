<?php

namespace Bank\Service;

use Application\Service\Result;
use Bank\Entity\BankEntity;
use Bank\Entity\RecordEntity;
use Bank\Exception\DeleteErrorException;
use Bank\Exception\NotFoundException;
use Bank\Exception\SaveErrorException;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetSharedDate;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\Strategy\DateTimeFormatterStrategy;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class RecordManager {

    const TABLE_RECORDS = 'banks_records';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var BankManager
     */
    protected $bankManager;

    /**
     * RecordManager constructor.
     * @param Adapter $adapter
     * @param HydratorInterface $hydrator
     * @param BankManager $bankManager
     */
    public function __construct(Adapter $adapter, HydratorInterface $hydrator, BankManager $bankManager) {
        $this->adapter = $adapter;
        $this->hydrator = $hydrator;
        $this->bankManager = $bankManager;
    }

    /**
     * @param $companyId
     * @return int
     * @throws NotFoundException
     */
    public function getCurrentTotalAmount($companyId) {
        $companyId = intval($companyId);
        $date = (new \DateTime())->setTime(23, 59, 59);
        $sql = new Sql($this->adapter);

        $subSel = $sql->select(self::TABLE_RECORDS);
        $subSel->columns(['record_id', 'bank_id', 'date' => new Expression('MAX(date)')]);
        $subSel->where->equalTo('company_id', $companyId);
        $subSel->where->lessThanOrEqualTo('date', $date->format('Y-m-d'));
        $subSel->group('bank_id');

        $select = $sql->select(['t1' => self::TABLE_RECORDS]);
        $select->columns(['record_id', 'bank_id', 'amount', 'date']);
        $select->join(['t2' => $subSel], 't1.bank_id = t2.bank_id AND t1.date = t2.date');
        $select->where->equalTo('company_id', $companyId);


        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. No records found');
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

        $total = 0;
        //** @var RecordEntity $record */
        foreach ($resultSet as $record) {
            $total += $record->offsetGet('amount');
        }
        return $total;
    }

    /**
     * @param $companyId
     * @param \DateTime|null $date
     * @return ResultSet
     * @throws NotFoundException
     */
    public function getBankAmountRowset($companyId, \DateTime $date = null) {
        $companyId = intval($companyId);
        if (!$date instanceof \DateTime) {
            $date = (new \DateTime())->setTime(23, 59, 59);
        }
        $sql = new Sql($this->adapter);

        $subSel = $sql->select(self::TABLE_RECORDS);
        $subSel->columns(['record_id', 'bank_id', 'date' => new Expression('MAX(date)')]);
        $subSel->where->equalTo('company_id', $companyId);
        $subSel->where->lessThanOrEqualTo('date', $date->format('Y-m-d'));
        $subSel->group('bank_id');

        $select = $sql->select(['t1' => self::TABLE_RECORDS]);
        $select->columns(['amount']);
        $select->join(['t2' => $subSel], 't1.bank_id = t2.bank_id AND t1.date = t2.date');
        $select->join(['t3' => BankManager::TABLE_BANKS], 't1.bank_id = t3.bank_id', ['name']);
        $select->where->equalTo('company_id', $companyId);


        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. No records found');
        $resultSet = new ResultSet();
        $resultSet->initialize($dataSource);

        return $resultSet;
    }

    /**
     * @param      $companyId
     * @param null $start
     * @param null $end
     * @return array
     * @throws NotFoundException
     */
    public function getChessData($companyId, $start = null, $end = null) {

        $banks = $this->bankManager->fetchAllWithPaginator();
        $banks->setItemCountPerPage(100);

        $bankHeader = ['Дата / Банк'];
        $bankIndexes = [];
        /** @var BankEntity $bank */
        foreach ($banks as $bank) {
            $bankHeader['bank_id_' . $bank->getBankId()] = $bank->getName();
            $bankIndexes['bank_id_' . $bank->getBankId()] = ['bank_id' => $bank->getBankId()];
        }

        $bankHeader[] = 'ИТОГО';


        if (trim($start) && trim($end)) {
            $startDate = \DateTime::createFromFormat('d.m.Y', $start);
            $endDate = \DateTime::createFromFormat('d.m.Y', $end);
        } else {
            $startDate = new \DateTime('first day of this month');
            $endDate = new \DateTime('last day of this month');
        }

        $iteratorDate = clone $startDate;

        $diff = $endDate->diff($startDate)->format("%a");

        $dates = [];
        $totals = [];

        $interval = new \DateInterval('P1D');
        for ($i = 0; $i <= $diff; ++$i) {
            $bankIndexes = array_map(function ($bank) use ($iteratorDate) {
                $bank['amount'] = 0;
                $bank['class'] = 'amount-previous';
                //$bank['date'] = $iteratorDate;
                return $bank;
            }, $bankIndexes);
            $dates[$iteratorDate->format('Y-m-d')] = array_merge(['date' => $iteratorDate->format('d.m.Y')], $bankIndexes);

            //$totals[$iteratorDate->format('Y-m-d')]['total'] = 0;

            $iteratorDate->add($interval);
        }


        $records = $this->fetchRecordsLessThanOrEqualDate($companyId, $endDate);

        $previousAmount = [];
        /** @var RecordEntity $record */
        foreach ($records as $record) {
            $bankIndex = 'bank_id_' . $record->getBankId();
            $dateIndex = $record->getDate()->format('Y-m-d');

            if (false == array_key_exists($bankIndex, $previousAmount)) {
                $previousAmount[$bankIndex] = 0;
            }

            if (0 < $record->getAmount()) {
                $previousAmount[$bankIndex] = $record->getAmount();
            }


            if (key_exists($record->getDate()->format('Y-m-d'), $dates) && key_exists($bankIndex, $dates[$dateIndex])) {
                $dates[$dateIndex][$bankIndex] = [
                    'amount'    => $record->getAmount() ? $record->getAmount() : $previousAmount[$bankIndex],
                    'class'     => $record->getAmount() ? 'amount-current' : 'amount-previous',
                    'bank_id'   => $record->getBankId(),
                    'date'      => $record->getDate(),
                    'record_id' => $record->getRecordId(),
                ];
            }

        }

        $previousAmount = [];
        foreach ($dates as &$date) {
            //echo '<pre style="font-size:10px">';print_r($date);echo '</pre>';

            $total = 0;
            foreach ($date as $key => &$bank) {
                if (is_array($bank)) {

                    if (false == array_key_exists($key, $previousAmount)) {
                        $previousAmount[$key] = 0;
                    }

                    if (0 < $bank['amount']) {
                        $previousAmount[$key] = $bank['amount'];
                    }

                    if (!$bank['amount']) {
                        $bank['amount'] = $previousAmount[$key];
                    }
                    $total = bcadd($total, $bank['amount'], 4);
                    $bank['date'] = \DateTime::createFromFormat('d.m.Y', $date['date']);
                    //echo '<pre style="font-size:10px;">'; print_r($bank); echo '</pre>';
                }
            }
            $date['total'] = $total;
        }
        //exit;


        return array_merge([$bankHeader], $dates);


    }

    /**
     * @param int $companyId
     * @param \DateTime $start
     * @param \DateTime $end
     * @return HydratingResultSet
     * @throws NotFoundException
     */
    public function fetchAllBetweenDate(int $companyId, \DateTime $start, \DateTime $end) {
        $companyId = intval($companyId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_RECORDS);
        $select->where->equalTo('company_id', $companyId);
        $select->where->greaterThanOrEqualTo('date', $start->format('Y-m-d'));
        $select->where->lessThanOrEqualTo('date', $end->format('Y-m-d'));
        $select->order('date ASC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. No records found');
        $resultSet = new HydratingResultSet($this->hydrator, new RecordEntity());
        $resultSet->initialize($dataSource);
        return $resultSet;
    }

    /**
     * @param int $companyId
     * @param \DateTime $dateTime
     * @return HydratingResultSet
     * @throws NotFoundException
     */
    public function fetchRecordsLessThanOrEqualDate(int $companyId, \DateTime $dateTime) {
        $companyId = intval($companyId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_RECORDS);
        $select->where->equalTo('company_id', $companyId);
        $select->where->lessThanOrEqualTo('date', $dateTime->format('Y-m-d'));
        $select->order('date ASC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. No records found');
        $resultSet = new HydratingResultSet($this->hydrator, new RecordEntity());
        $resultSet->initialize($dataSource);
        return $resultSet;
    }

    /**
     * @param int $companyId
     * @return Paginator
     * @throws NotFoundException
     */
    public function fetchAllWithPaginator(int $companyId) {
        $companyId = intval($companyId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_RECORDS);
        $select->where->equalTo('company_id', $companyId);
        $select->order('date DESC');
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult())
            throw new NotFoundException('Unknown error. No records found');

        $resultSet = new HydratingResultSet($this->hydrator, new RecordEntity());
        $paginator = new Paginator(new DbSelect($select, $sql, $resultSet));
        return $paginator;
    }

    /**
     * @param $recordId
     * @return RecordEntity
     * @throws NotFoundException
     */
    public function fetchOneById($recordId) {
        $recordId = intval($recordId);
        $sql = new Sql($this->adapter);
        $select = $sql->select(self::TABLE_RECORDS);
        $select->where->equalTo('record_id', $recordId);
        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();
        if (!$dataSource->isQueryResult() || !$dataSource->count())
            throw new NotFoundException('No records found');
        $resultSet = new HydratingResultSet($this->hydrator, new RecordEntity());
        $resultSet->initialize($dataSource);
        /** @var RecordEntity $recordEntity */
        $recordEntity = $resultSet->current();
        return $recordEntity;
    }

    /**
     * @param RecordEntity $recordEntity
     * @return RecordEntity
     * @throws SaveErrorException
     */
    public function saveRecord(RecordEntity $recordEntity) {
        $data = $this->hydrator->extract($recordEntity);

        $sql = new Sql($this->adapter, self::TABLE_RECORDS);
        if ($recordId = $recordEntity->getRecordId()) {
            unset($data['created']);
            $action = $sql->update();
            $action->set($data);
            $action->where->equalTo('record_id', $recordId);
        } else {
            $action = $sql->insert();
            $action->values($data);
        }
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->getAffectedRows())
            throw new SaveErrorException('Unknown error. The record data was not saved');
        if ($recordId = $result->getGeneratedValue())
            $recordEntity->setRecordId($recordId);
        return $recordEntity;
    }


    /**
     * @param int $recordId
     * @throws DeleteErrorException
     */
    public function deleteRecord(int $recordId) {
        $recordId = intval($recordId);
        $sql = new Sql($this->adapter, self::TABLE_RECORDS);
        $action = $sql->delete();
        $action->where->equalTo('record_id', $recordId);
        $result = $sql->prepareStatementForSqlObject($action)->execute();
        if (!$result->getAffectedRows())
            throw new DeleteErrorException('Unknown error. The record data was not deleted');
        return;
    }


    /**
     * @param $file
     * @return Result
     * @throws \Exception
     */
    public function putImportData($companyId, $file) {

        $connection = $this->adapter->driver->getConnection();
        $connection->beginTransaction();


        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            //$spreadsheet = $reader->load('./www/export/43453-purchase-wagons-export.xlsx');
            $spreadsheet = $reader->load($file['tmp_name']);

            $sheet = $spreadsheet->getActiveSheet();

            $valueOptions = $this->bankManager->fetchValueOptions();

            foreach ($sheet->getRowIterator() as $key => $row) {
                $rowIndex = $row->getRowIndex();
                if ($rowIndex < 2) continue;

                $bankName = trim($sheet->getCell(sprintf('A%d', $rowIndex))->getValue());

                if (!$bankId = array_search($bankName, $valueOptions)) {
                    $errors[] = sprintf('Банк не найден в строке %d', $rowIndex);
                    continue;
                }

                $date = $sheet->getCell(sprintf('B%d', $rowIndex))->getValue();

                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
                    $date = \DateTime::createFromFormat('d.m.Y', $date);
                } else {
                    $date = PhpSpreadsheetSharedDate::excelToDateTimeObject($date);
                }

                $amount = $sheet->getCell(sprintf('C%d', $rowIndex))->getValue();

                $entity = new RecordEntity();
                $entity->setAmount($amount);
                $entity->setBankId($bankId);
                $entity->setCompanyId($companyId);
                $entity->setDate($date);

                // Delete exists record
                $sql = new Sql($this->adapter, self::TABLE_RECORDS);
                $delete = $sql->delete();
                $delete->where->equalTo('bank_id', $bankId);
                $delete->where->equalTo('company_id', $companyId);
                $delete->where->equalTo('date', $date->format('Y-m-d'));

                $sql->prepareStatementForSqlObject($delete)->execute();

                // Save new record
                $this->saveRecord($entity);
            }

            $connection->commit();

        } catch (\PhpOffice\PhpSpreadsheet\Exception $exception) {
            return new Result('error', sprintf('The bank data was not imported. (%s)', $exception->getMessage()));
        } catch (\InvalidArgumentException $exception) {
            return new Result('error', sprintf('The bank data was not imported. (%s)', $exception->getMessage()));
        }

        $errors = [];
        if (0 < count($errors)) {
            $connection->rollback();
            return new Result('warning', 'Some bank data was not imported.', $errors);
        }
        return new Result('success', 'All bank data was successfully imported.');


    }

}