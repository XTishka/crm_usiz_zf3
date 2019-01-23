<?php
/**
 * Created by Bogdan Tereshchenko <development.sites@gmail.com>
 * Copyright: 2006-2019 Bogdan Tereshchenko
 * Link: https://zelliengroup.com/
 */

namespace Contractor\Form\Element;


use Contractor\Entity\ContractorAbstract;
use Zend\Form\Element\Select;

class ContractorTypeSelect extends Select {

    public function init() {

        $this->setValueOptions([
            ContractorAbstract::TYPE_ADDITIONAL => 'Additional',
            ContractorAbstract::TYPE_CARRIER    => 'Carrier',
            ContractorAbstract::TYPE_COMPANY    => 'Company',
            ContractorAbstract::TYPE_CONSIGNEE  => 'Consignee',
            ContractorAbstract::TYPE_CORPORATE  => 'Corporate',
            ContractorAbstract::TYPE_CUSTOMER   => 'Customer',
            ContractorAbstract::TYPE_PLANT      => 'Plant',
            ContractorAbstract::TYPE_PROVIDER   => 'Provider',
        ]);

    }

}