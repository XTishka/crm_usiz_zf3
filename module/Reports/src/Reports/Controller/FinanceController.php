<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 */

namespace Reports\Controller;

use Contractor\Entity\ContractorAbstract;
use Contractor\Service\Repository\DatabaseContractorAbstract;
use Document\Domain\TransactionEntity;
use Document\Service\Repository\FinanceTransactionDb;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Reports\Form\FinanceFilterForm;
use Transport\Service\Repository\CarrierDb;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FinanceController extends AbstractActionController {

    /**
     * @var Request
     */
    protected $request;

    /** @var Response */
    protected $response;

    /**
     * @var FinanceFilterForm
     */
    protected $form;

    /**
     * @var Adapter
     */
    protected $db;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * FinanceController constructor.
     * @param FinanceFilterForm $form
     * @param Adapter $db
     * @param Translator $translator
     */
    public function __construct(FinanceFilterForm $form, Adapter $db, Translator $translator) {
        $this->form = $form;
        $this->db = $db;
        $this->translator = $translator;
    }

    /**
     * @return Response|ViewModel
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function indexAction() {
        $viewModel = new ViewModel();
        if ($this->request->isPost()) {
            $this->form->setData($this->params()->fromPost());
            if ($this->form->isValid()) {

                if ($this->params()->fromPost('submit')) {
                    $output = $this->getReport($this->form->getData());
                    $this->response->getHeaders()->addHeaders([
                        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment;filename="finance-report.xlsx"',
                        'Cache-Control'       => 'max-age=0',
                    ]);
                    $this->response->setStatusCode(200);
                    $this->response->setContent($output);
                    return $this->response;
                }

                $viewModel->setVariable('logs', $this->getTransactions($this->form->getData()));
            }
        }

        $viewModel->setVariable('form', $this->form);
        return $viewModel;
    }

    public function getTransactions(array $params) {

        $sql = new Sql($this->db);

        $selectA = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectA->join(['b' => DatabaseContractorAbstract::TABLE_CONTRACTORS], 'a.contractor_id = b.contractor_id AND a.contractor_type = b.contractor_type', [
            'contractor_name',
        ]);
        $selectA->where->notEqualTo('a.contractor_type', ContractorAbstract::TYPE_CARRIER);
        $selectA->group('a.transaction_id');

        $selectB = $sql->select(['a' => FinanceTransactionDb::TABLE_FINANCE_TRANSACTIONS]);
        $selectB->join(['b' => CarrierDb::TABLE_CARRIERS], new Expression('a.contractor_id = b.carrier_id'), [
            'contractor_name' => 'carrier_name',
        ]);
        $selectB->where->equalTo('a.contractor_type', ContractorAbstract::TYPE_CARRIER);


        if (key_exists('company_id', $params) && $params['company_id']) {
            $selectA->where->equalTo('a.company_id', intval($params['company_id']));
            $selectB->where->equalTo('a.company_id', intval($params['company_id']));
        }

        if (key_exists('contractor_type', $params) && $params['contractor_type']) {
            $selectA->where->equalTo('a.contractor_type', trim($params['contractor_type']));
        }

        if (key_exists('contractor_id', $params) && $params['contractor_id']) {
            if (preg_match('/^(con|car)_\d+$/u', $params['contractor_id'], $matches)) {
                if ('con' == $matches[1]) {
                    $selectA->where->equalTo('a.contractor_id', preg_replace('/^\w+(\d+)/', '$1', $params['contractor_id']));
                } elseif ('car' == $matches[1]) {
                    $selectB->where->equalTo('a.contractor_id', preg_replace('/^\w+(\d+)/', '$1', $params['contractor_id']));
                }
            }
        }

        if (key_exists('transaction_route', $params) && $params['transaction_route']) {
            if ('debit' == $params['transaction_route']) {
                $selectA->where->greaterThan('a.debit', 0);
                $selectB->where->greaterThan('a.debit', 0);
            } elseif ('credit' == $params['transaction_route']) {
                $selectA->where->greaterThan('a.credit', 0);
                $selectB->where->greaterThan('a.credit', 0);
            }
        }

        if (key_exists('transaction_type', $params) && $params['transaction_type']) {
            $selectA->where->equalTo('a.transaction_type', $params['transaction_type']);
            $selectB->where->equalTo('a.transaction_type', $params['transaction_type']);
        }

        if (key_exists('period_begin', $params) && key_exists('period_end', $params)) {
            $pBegin = \DateTime::createFromFormat('d.m.Y', $params['period_begin']);
            $pEnd = \DateTime::createFromFormat('d.m.Y', $params['period_end']);
            $selectA->where->nest()
                ->greaterThanOrEqualTo(new Expression('DATE(a.created)'), $pBegin->format('Y-m-d'))
                ->and
                ->lessThanOrEqualTo(new Expression('DATE(a.created)'), $pEnd->format('Y-m-d'));
            $selectB->where->nest()
                ->greaterThanOrEqualTo(new Expression('DATE(a.created)'), $pBegin->format('Y-m-d'))
                ->and
                ->lessThanOrEqualTo(new Expression('DATE(a.created)'), $pEnd->format('Y-m-d'));
        }

        $select = $sql->select(['a' => $selectA->combine($selectB)]);
        $select->order('created DESC');

        //echo $select->getSqlString($this->db->platform);

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $resultSet = new ResultSet($dataSource);
        $resultSet->initialize($dataSource);

        //print_r($params);
        return $resultSet;
    }

    /**
     * @param array $params
     * @return false|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function getReport(array $params) {
        $transactions = $this->getTransactions($params);

        $spreadsheet = new Spreadsheet();

        // A B C D E F G H I K L M N O P Q R S T V X Y Z

        $sheet = $spreadsheet->getSheet(0);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $index = 0;

        $sheet->fromArray(['Контрагент', 'Дебет', 'Кредит', 'Тип операции', 'Дата'], null, sprintf('A%s', ++$index));
        $sheet->getStyle(sprintf('A%1$s:E%1$s', $index))->getFont()->setBold(true)->setSize(10);

        $colorRed = new Color('CA3C3C');
        $colorGray = new Color('333333');
        $colorGreen = new Color('1CB841');

        foreach ($transactions as $log) {
            $index++;

            $sheet->getCell(sprintf('A%d', $index))->setValue($log->offsetGet('contractor_name'));

            $sheet->getCell(sprintf('B%d', $index))->setValue($log->offsetGet('debit'));
            $sheet->getCell(sprintf('B%d', $index))->getStyle()->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');

            $sheet->getCell(sprintf('C%d', $index))->setValue($log->offsetGet('credit'));
            $sheet->getCell(sprintf('C%d', $index))->getStyle()->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');

            if (TransactionEntity::TRANSACTION_DEBT === $log->offsetGet('transaction_type')):
                $sheet->getCell(sprintf('D%d', $index))->getStyle()->getFont()->setColor($colorRed);
                $sheet->getCell(sprintf('D%d', $index))->setValue($this->translator->translate('Debt'));
            elseif (TransactionEntity::TRANSACTION_PAYMENT === $log->offsetGet('transaction_type')):
                $sheet->getCell(sprintf('D%d', $index))->getStyle()->getFont()->setColor($colorGreen);
                $sheet->getCell(sprintf('D%d', $index))->setValue($this->translator->translate('Payment'));
            else:
                $sheet->getCell(sprintf('D%d', $index))->getStyle()->getFont()->setColor($colorGray);
                $sheet->getCell(sprintf('D%d', $index))->setValue($this->translator->translate('Unsupported transaction type'));
            endif;

            $sheet->getCell(sprintf('E%d', $index))->setValue((\DateTime::createFromFormat('Y-m-d H:i:s', $log->offsetGet('created')))->format('d.m.Y'));
            $sheet->getCell(sprintf('E%d', $index))->getStyle()->getNumberFormat()->setFormatCode('dd.mm.yyyy');

        }

        $writer = new XlsxWriter($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }


}