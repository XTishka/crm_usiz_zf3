<?php

namespace Document\Service;

use Application\Service;
use Document\Domain\SaleWagonEntity;
use Document\Service\Rate\AdapterFactory;
use Document\Service\Rate\MixedAdapter;
use Transport\Service\RateManager;

class SaleRecountShippingCost {

    /** @var RateManager */
    protected $rateManager;

    /** @var SaleWagonManager */
    protected $saleWagonManager;

    /** @var Service\TaxManager */
    protected $taxManager;

    /**
     * SaleRecountShippingCost constructor.
     * @param RateManager $rateManager
     * @param SaleWagonManager $saleWagonManager
     * @param Service\TaxManager $taxManager
     */
    public function __construct(RateManager $rateManager, SaleWagonManager $saleWagonManager, Service\TaxManager $taxManager) {
        $this->rateManager = $rateManager;
        $this->saleWagonManager = $saleWagonManager;
        $this->taxManager = $taxManager;
    }

    public function recount($rateId) {
        try {
            $wagons = $this->saleWagonManager->getWagonsByRateId($rateId);
            $taxValue = $this->taxManager->getCurrentTaxValue();
            $counter = 0;
            /** @var SaleWagonEntity $wagon */
            foreach ($wagons as $wagon) {
                if (($rateId = $wagon->getRateId()) && ($rateValueId = $wagon->getRateValueId())) {
                    $transportRate = $this->rateManager->getRateById($rateId);
                    $transportRateValue = $this->rateManager->getRateValueById($rateValueId);
                    $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRateValue->getPrice(), $wagon->getLoadingWeight());
                    if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                        $rateAdapter->setMinWeight($minWeight);
                    }
                    $shipping = $rateAdapter->calculate($taxValue);
                    $wagon->setTransportPrice($shipping);
                    $this->saleWagonManager->saveWagon($wagon);
                    $counter++;
                }
            }
        } catch (\Exception $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Sale shipping cost recalculation completed successfully', $counter);
    }

}