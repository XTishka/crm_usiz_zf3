<?php

namespace Reports\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Reports\Form\ShipmentsFilterForm;
use Transport\Service\RateManager;
use Zend\Db\Adapter\Adapter as DatabaseAdapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Db\Sql\Sql;
use Zend\EventManager\Filter\FilterIterator;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ShipmentsController extends AbstractActionController {

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var ShipmentsFilterForm */
    protected $formShipmentsFilter;

    /** @var DatabaseAdapter */
    protected $db;

    protected $rateManager;

    /**
     * ShipmentsController constructor.
     * @param DatabaseAdapter     $db
     * @param RateManager         $rateManager
     * @param ShipmentsFilterForm $formShipmentsFilter
     */
    public function __construct(ShipmentsFilterForm $formShipmentsFilter, DatabaseAdapter $db, RateManager $rateManager) {
        $this->formShipmentsFilter = $formShipmentsFilter;
        $this->db = $db;
        $this->rateManager = $rateManager;
    }

    /**
     * @return Response|ViewModel
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function indexAction() {
        $form = $this->formShipmentsFilter;
        $viewModel = new ViewModel();

        if ($this->request->isPost()) {
            $rawData = $this->params()->fromPost();
            $form->setData($rawData);
            if ($form->isValid()) {
                $filteredData = $form->getData();

                if ($this->params()->fromPost('submit')) {
                    $output = $this->getReport($filteredData);
                    $this->response->getHeaders()->addHeaders([
                        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment;filename="shipments-report.xlsx"',
                        'Cache-Control'       => 'max-age=0',
                    ]);
                    $this->response->setStatusCode(200);
                    $this->response->setContent($output);
                    return $this->response;
                }


                $viewModel->setVariable('wagons', $this->getData($filteredData));

            }
        }

        $viewModel->setVariable('form', $form);
        $viewModel->setVariable('messenger', $this->plugin('flashMessenger'));
        return $viewModel;
    }

    public function getData(array $data) {

        $sql = new Sql($this->db);
        $select = $sql->select();
        $select->from(['a' => 'sale_wagons']);
        $select->columns(['wagon_number', 'loading_date', 'loading_weight', 'product_price', 'transport_price']);
        $select->join(['b' => 'sale_contracts'], 'a.contract_id = b.contract_id', ['contract_price_without_tax' => 'price', 'tax']);
        $select->join(['c' => 'contractors'], 'b.customer_id = c.contractor_id', ['customer' => 'contractor_name'], Join::JOIN_LEFT);
        $select->join(['d' => 'contractors'], 'b.consignee_id = d.contractor_id', ['consignee' => 'contractor_name'], Join::JOIN_LEFT);
        $select->join(['e' => 'products'], 'b.product_id = e.product_id', ['product' => 'product_name']);
        $select->join(['f' => 'rates'], 'a.rate_id = f.rate_id', [], Join::JOIN_LEFT);
        $select->join(['g' => 'rates_values'], 'f.rate_id = g.rate_id', ['transport_price' => 'price'], Join::JOIN_LEFT);
        $select->join(['h' => 'stations'], 'f.station_id = h.station_id', ['station' => 'station_name'], Join::JOIN_LEFT);
        $select->join(['i' => 'carriers'], 'a.carrier_id = i.carrier_id', ['carrier' => 'carrier_name'], Join::JOIN_LEFT);

        $select->group('a.wagon_id');

        if (key_exists('company_id', $data)) {
            $select->where->equalTo('b.company_id', $data['company_id']);
        }

        if (key_exists('carrier_id', $data) && $data['carrier_id']) {
            $select->where->equalTo('a.carrier_id', $data['carrier_id']);
        }

        if (key_exists('contractor_id', $data) && $data['contractor_id']) {
            $select->where->equalTo('b.customer_id', $data['contractor_id']);
        }

        if (key_exists('consignee_id', $data) && $data['consignee_id']) {
            $select->where->equalTo('b.consignee_id', $data['consignee_id']);
        }

        if (key_exists('product_id', $data) && $data['product_id']) {
            $select->where->equalTo('b.product_id', $data['product_id']);
        }

        if (key_exists('period_begin', $data) && key_exists('period_end', $data)) {
            $pBegin = \DateTime::createFromFormat('d.m.Y', $data['period_begin']);
            $pEnd = \DateTime::createFromFormat('d.m.Y', $data['period_end']);
            $select->where->nest()
                ->greaterThanOrEqualTo('a.loading_date', $pBegin->format('Y-m-d'))->and
                ->lessThanOrEqualTo('a.loading_date', $pEnd->format('Y-m-d'));
        }

        $dataSource = $sql->prepareStatementForSqlObject($select)->execute();

        $result = new ResultSet();
        $result->initialize($dataSource);

        $iterator = new \ArrayIterator($result->toArray());

        $filteredWagons = new \CallbackFilterIterator($iterator, function (&$wagon) {
            $wagon['loading_date'] = \DateTime::createFromFormat('Y-m-d', $wagon['loading_date']);
            $wagon['price_per_ton'] = bcdiv($wagon['transport_price'], $wagon['loading_weight'], 2);
            $wagon['transport_price_without_tax'] = bcmul($wagon['transport_price'], bcmul((100 - $wagon['tax']), 0.01, 2), 2);
            $wagon['product_price_without_tax'] = bcmul($wagon['product_price'], bcmul((100 - $wagon['tax']), 0.01, 2), 2);

            $wagon['contract_price'] = bcadd($wagon['contract_price_without_tax'], bcmul($wagon['contract_price_without_tax'], bcmul($wagon['tax'], 0.01, 2), 2), 2);

            $wagon['total_price'] = bcadd($wagon['product_price'], $wagon['transport_price'], 2);
            $wagon['total_price_without_tax'] = bcadd($wagon['product_price_without_tax'], $wagon['transport_price_without_tax'], 2);
            ksort($wagon);
            return $wagon;
        });

        return iterator_to_array($filteredWagons);
    }

    /**
     * @param array $data
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function getReport(array $data) {
        $filteredWagons = $this->getData($data);

        $spreadsheet = new Spreadsheet();

        // A B C D E F G H I K L M N O P Q R S T V X Y Z

        $sheet = $spreadsheet->getSheet(0);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);

        $index = 0;

        $sheet->fromArray([
            'Дата',
            'Номер вагона',
            'Грузополучатель',
            'Покупатель',
            'Перевозчик',
            'Станция получателя',
            'Номенклатура',
            'Отгружено, т.',
            'Стоимость перевозки за 1т',
            'Тариф без НДС',
            'Тариф с НДС',
            'Цена по договору без НДС',
            'Цена по договору с НДС',
            'Чистая цена без НДС',
            'Чистая цена С НДС',
            'Итого без НДС',
            'Итого с НДС',
        ], null, sprintf('A%s', ++$index));

        $sheet->getStyle(sprintf('A%1$s:Q%1$s', $index))->getFont()->setBold(true);

        foreach ($filteredWagons as $wagon) {
            $index++;

            $sheet->getCell(sprintf('A%d', $index))->setValue($wagon['loading_date']->format('d.m.Y'));
            $sheet->getStyle(sprintf('A%d', $index))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $sheet->getCell(sprintf('B%d', $index))->setValue($wagon['wagon_number']);
            $sheet->getCell(sprintf('C%d', $index))->setValue($wagon['consignee']);
            $sheet->getCell(sprintf('D%d', $index))->setValue($wagon['customer']);
            $sheet->getCell(sprintf('E%d', $index))->setValue($wagon['carrier']);
            $sheet->getCell(sprintf('F%d', $index))->setValue($wagon['station']);
            $sheet->getCell(sprintf('G%d', $index))->setValue($wagon['product']);
            $sheet->getCell(sprintf('H%d', $index))->setValue($wagon['loading_weight']);
            $sheet->getStyle(sprintf('H%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"т."');
            $sheet->getCell(sprintf('I%d', $index))->setValue($wagon['price_per_ton']);
            $sheet->getStyle(sprintf('I%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('J%d', $index))->setValue($wagon['transport_price_without_tax']);
            $sheet->getStyle(sprintf('J%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('K%d', $index))->setValue($wagon['transport_price']);
            $sheet->getStyle(sprintf('K%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('L%d', $index))->setValue($wagon['contract_price_without_tax']);
            $sheet->getStyle(sprintf('L%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('M%d', $index))->setValue($wagon['contract_price']);
            $sheet->getStyle(sprintf('M%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('N%d', $index))->setValue($wagon['product_price_without_tax']);
            $sheet->getStyle(sprintf('N%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('O%d', $index))->setValue($wagon['product_price']);
            $sheet->getStyle(sprintf('O%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('P%d', $index))->setValue($wagon['total_price_without_tax']);
            $sheet->getStyle(sprintf('P%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('Q%d', $index))->setValue($wagon['total_price']);
            $sheet->getStyle(sprintf('Q%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
        }


        $writer = new XlsxWriter($spreadsheet);
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }

}