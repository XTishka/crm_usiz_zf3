<?php

namespace Reports\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Reports\Form\PurchaseWagonsFilterForm;
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

class PurchaseWagonsController extends AbstractActionController {

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var PurchaseWagonsFilterForm */
    protected $formPurchaseWagonsFilter;

    /** @var DatabaseAdapter */
    protected $db;

    protected $rateManager;

    /**
     * PurchaseWagonsController constructor.
     * @param PurchaseWagonsFilterForm $formPurchaseWagonsFilter
     * @param DatabaseAdapter $db
     * @param RateManager $rateManager
     */
    public function __construct(PurchaseWagonsFilterForm $formPurchaseWagonsFilter, DatabaseAdapter $db, RateManager $rateManager) {
        $this->formPurchaseWagonsFilter = $formPurchaseWagonsFilter;
        $this->db = $db;
        $this->rateManager = $rateManager;
    }

    /**
     * @return Response|ViewModel
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function indexAction() {
        $form = $this->formPurchaseWagonsFilter;
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
                        'Content-Disposition' => 'attachment;filename="purchase-wagons-report.xlsx"',
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
        $select->from(['a' => 'purchase_wagons']);
        $select->columns(['wagon_number', 'loading_date', 'unloading_date', 'loading_weight', 'unloading_weight', 'material_price', 'status', 'transport_price' => 'delivery_price']);
        $select->join(['b' => 'purchase_contracts'], 'a.contract_id = b.contract_id', ['contract_price_without_tax' => 'price', 'tax']);
        $select->join(['c' => 'contractors'], 'b.provider_id = c.contractor_id', ['provider' => 'contractor_name'], Join::JOIN_LEFT);
        $select->join(['e' => 'materials'], 'b.material_id = e.material_id', ['material' => 'material_name']);
        $select->join(['f' => 'rates'], 'a.rate_id = f.rate_id', [], Join::JOIN_LEFT);
        //$select->join(['g' => 'rates_values'], 'f.rate_id = g.rate_id', ['transport_price' => 'price']);
        $select->join(['h' => 'stations'], 'f.station_id = h.station_id', ['station' => 'station_name'], Join::JOIN_LEFT);
        $select->join(['i' => 'carriers'], 'a.carrier_id = i.carrier_id', ['carrier' => 'carrier_name'], Join::JOIN_LEFT);

        $select->group('a.wagon_id');

        if (key_exists('company_id', $data)) {
            $select->where->equalTo('b.company_id', $data['company_id']);
        }

        if (key_exists('carrier_id', $data) && $data['carrier_id']) {
            $select->where->equalTo('a.carrier_id', $data['carrier_id']);
        }

        if (key_exists('provier_id', $data) && $data['provier_id']) {
            $select->where->equalTo('b.provier_id', $data['provier_id']);
        }

        if (key_exists('material_id', $data) && $data['material_id']) {
            $select->where->equalTo('b.material_id', $data['material_id']);
        }

        if (key_exists('status', $data) && $data['status']) {
            $select->where->equalTo('a.status', $data['status']);
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
            $wagon['unloading_date'] = $wagon['unloading_date'] ? \DateTime::createFromFormat('Y-m-d', $wagon['unloading_date']) : null;
            $wagon['price_per_ton'] = bcdiv($wagon['transport_price'], $wagon['loading_weight'], 2);
            $wagon['transport_price_without_tax'] = bcmul($wagon['transport_price'], bcmul((100 - $wagon['tax']), 0.01, 2), 2);
            $wagon['material_price_without_tax'] = bcmul($wagon['material_price'], bcmul((100 - $wagon['tax']), 0.01, 2), 2);

            $wagon['contract_price'] = bcadd($wagon['contract_price_without_tax'], bcmul($wagon['contract_price_without_tax'], bcmul($wagon['tax'], 0.01, 2), 2), 2);

            $wagon['total_price'] = bcadd($wagon['material_price'], $wagon['transport_price'], 2);
            $wagon['total_price_without_tax'] = bcadd($wagon['material_price_without_tax'], $wagon['transport_price_without_tax'], 2);
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
            'Номер вагона',                     // A
            'Номенклатура',                     // B
            'Поставщик',                        // C
            'Дата загрузки',                    // D
            'Вес загрузки',                     // E
            'Дата разгрузки',                   // F
            'Вес разгрузки',                    // G
            'Перевозчик',                       // H
            'Стоимость перевозки за тонну',     // I
            'Тариф без НДС',                    // J
            'Тариф с НДС',                      // K
            'Цена по договору без НДС',         // L
            'Цена по договору с НДС',           // M
            'Чистая цена без НДС',              // N
            'Чистая цена с НДС',                // O
            'Итого без НДС',                    // P
            'Итого с НДС',                      // Q
        ], null, sprintf('A%s', ++$index));

        $sheet->getStyle(sprintf('A%1$s:Q%1$s', $index))->getFont()->setBold(true);

        foreach ($filteredWagons as $wagon) {
            $index++;

            $sheet->getCell(sprintf('A%d', $index))->setValue($wagon['wagon_number']);
            $sheet->getCell(sprintf('B%d', $index))->setValue($wagon['material']);
            $sheet->getCell(sprintf('C%d', $index))->setValue($wagon['provider']);
            $sheet->getCell(sprintf('D%d', $index))->setValue($wagon['loading_date']->format('d.m.Y'));
            $sheet->getStyle(sprintf('D%d', $index))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $sheet->getCell(sprintf('E%d', $index))->setValue($wagon['loading_weight']);
            $sheet->getStyle(sprintf('E%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"т."');
            $sheet->getCell(sprintf('F%d', $index))->setValue(empty($wagon['unloading_date']) ? null : $wagon['unloading_date']->format('d.m.Y'));
            $sheet->getStyle(sprintf('F%d', $index))->getNumberFormat()->setFormatCode('dd.mm.yyyy');
            $sheet->getCell(sprintf('G%d', $index))->setValue($wagon['unloading_weight']);
            $sheet->getStyle(sprintf('G%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"т."');
            $sheet->getCell(sprintf('H%d', $index))->setValue($wagon['carrier']);
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
            $sheet->getCell(sprintf('N%d', $index))->setValue($wagon['material_price_without_tax']);
            $sheet->getStyle(sprintf('N%s', $index))->getNumberFormat()->setFormatCode('#,##0.00_-"грн."');
            $sheet->getCell(sprintf('O%d', $index))->setValue($wagon['material_price']);
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