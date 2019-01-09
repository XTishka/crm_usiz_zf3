<?php

namespace Document\Service;

use Application\Service;
use Document\Domain\PurchaseWagonEntity;
use Document\Exception;
use Document\Service\Rate\AdapterFactory;
use Document\Service\Rate\MixedAdapter;
use Document\Service\Repository\PurchaseWagonDb;
use Transport\Service\RateManager;

class PurchaseRecountShippingCost {

    /** @var RateManager */
    protected $rateManager;

    /** @var PurchaseWagonManager */
    protected $purchaseWagonManager;

    /** @var Service\TaxManager */
    protected $taxManager;

    /**
     * PurchaseRecountShippingCost constructor.
     * @param RateManager $rateManager
     * @param PurchaseWagonManager $purchaseWagonManager
     * @param Service\TaxManager $taxManager
     */
    public function __construct(RateManager $rateManager, PurchaseWagonManager $purchaseWagonManager, Service\TaxManager $taxManager) {
        $this->rateManager = $rateManager;
        $this->purchaseWagonManager = $purchaseWagonManager;
        $this->taxManager = $taxManager;
    }

    public function recount($rateId) {
        try {
            $wagons = $this->purchaseWagonManager->getWagonsByRateId($rateId);
            $taxValue = $this->taxManager->getCurrentTaxValue();
            $counter = 0;
            /** @var PurchaseWagonEntity $wagon */
            foreach ($wagons as $wagon) {
                if (($rateId = $wagon->getRateId()) && ($rateValueId = $wagon->getRateValueId())) {
                    $transportRate = $this->rateManager->getRateById($rateId);
                    $transportRateValue = $this->rateManager->getRateValueById($rateValueId);
                    $rateAdapter = AdapterFactory::create($transportRate->getRateType(), $transportRateValue->getPrice(), $wagon->getLoadingWeight());
                    if (($rateAdapter instanceof MixedAdapter) && ($minWeight = $transportRate->getMinWeight())) {
                        $rateAdapter->setMinWeight($minWeight);
                    }
                    $shipping = $rateAdapter->calculate($taxValue);
                    $wagon->setDeliveryPrice($shipping);
                    $this->purchaseWagonManager->saveWagon($wagon);
                    $counter++;
                }
            }
        } catch (\Exception $exception) {
            return new Service\Result(Service\Result::STATUS_ERROR, $exception->getMessage());
        }
        return new Service\Result(Service\Result::STATUS_SUCCESS, 'Purchase shipping cost recalculation completed successfully', $counter);
    }

}