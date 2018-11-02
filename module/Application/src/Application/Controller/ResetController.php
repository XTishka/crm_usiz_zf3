<?php

namespace Application\Controller;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;

class ResetController extends AbstractActionController {

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * ResetController constructor.
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function resetAction() {
        $this->adapter->query('TRUNCATE TABLE finances_logs')->execute();
        $this->adapter->query('TRUNCATE TABLE finance_transactions')->execute();
        $this->adapter->query('TRUNCATE TABLE purchase_wagons')->execute();
        $this->adapter->query('TRUNCATE TABLE sale_wagons')->execute();
        $this->adapter->query('TRUNCATE TABLE skip_common')->execute();
        $this->adapter->query('TRUNCATE TABLE skip_materials')->execute();
        $this->adapter->query('TRUNCATE TABLE warehouses_logs')->execute();

        $this->plugin('flashMessenger')->addSuccessMessage('База данных была очищена без договоров.');
        $this->redirect()->toRoute('home');
    }

    public function allAction() {
        $this->adapter->query('TRUNCATE TABLE finance_transactions')->execute();
        $this->adapter->query('TRUNCATE TABLE finances_logs')->execute();
        $this->adapter->query('TRUNCATE TABLE purchase_wagons')->execute();
        $this->adapter->query('TRUNCATE TABLE sale_wagons')->execute();
        $this->adapter->query('TRUNCATE TABLE skip_common')->execute();
        $this->adapter->query('TRUNCATE TABLE skip_materials')->execute();
        $this->adapter->query('TRUNCATE TABLE warehouses_logs')->execute();
        $this->adapter->query('TRUNCATE TABLE sale_contracts')->execute();
        $this->adapter->query('TRUNCATE TABLE purchase_contracts')->execute();

        $this->plugin('flashMessenger')->addSuccessMessage('База данных была полностью очищена');
        $this->redirect()->toRoute('home');
    }

}