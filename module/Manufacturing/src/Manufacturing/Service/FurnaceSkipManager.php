<?php

namespace Manufacturing\Service;

use Application\Service\Result;
use ArrayObject;
use DateTime;
use DateInterval;
use Document\Exception;
use Manufacturing\Domain\FurnaceSkipEntity;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Service\Repository\FurnaceDb;
use Manufacturing\Service\Repository\FurnaceLogDb;

class FurnaceSkipManager {

    protected $furnaceDb;

    /**
     * @var FurnaceLogDb
     */
    protected $furnaceLogDbRepository;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    public function __construct(FurnaceDb $furnaceDb, FurnaceLogDb $furnaceLogDbRepository, WarehouseLogManager $warehouseLogManager) {
        $this->furnaceDb = $furnaceDb;
        $this->furnaceLogDbRepository = $furnaceLogDbRepository;
        $this->warehouseLogManager = $warehouseLogManager;
    }

    public function getFurnaceLogByPlantId($plantId) {

        $furnaces = array_filter($this->furnaceDb->fetchFurnacesArray(), function ($furnace) use ($plantId) {
            return $furnace['plant_id'] == $plantId;
        });

        $dateCurrent = (new DateTime())->setTime(0, 0, 0, 0);
        $dateEnd = (clone $dateCurrent)->sub(new DateInterval('P30D'));

        $generatedArray = [];
        foreach ($furnaces as $key => $furnace) {
            $generatedArray[$key + 1] = ['furnace_id' => $furnace['furnace_id'], 'status' => 'error'];
        }

        $data = [];

        while ($dateEnd <= $dateCurrent) {
            $dateCurrentClone = clone $dateCurrent;
            $data[$dateCurrent->format('Y-m-d')] = [$dateCurrentClone] + $generatedArray;
            $dateCurrent->sub(new DateInterval('P1D'));
        }

        $header = ['Load date'];
        foreach ($furnaces as $furnaceNumber => $option) {
            $furnaceNumber += 1;
            $header[] = $option['furnace_name'];
            $skips = $this->furnaceLogDbRepository->fetchLogsArray($option['furnace_id'], $dateEnd);
            foreach ($skips as &$skip) {
                if (key_exists($skip['date'], $data) && key_exists($furnaceNumber, $data[$skip['date']])) {
                    $data[$skip['date']][$furnaceNumber] = [
                        'log_id'          => $skip['id'],
                        'furnace_id'      => $skip['furnace_id'],
                        'status'          => 'success',
                        'weight_material' => $skip['weight_material'],
                        'weight_coal'     => $skip['weight_coal'],
                    ];
                }
                unset($skip);
            }
        }

        array_unshift($data, $header);

        return $data;

    }

    public function loading(FurnaceSkipEntity $object) {
        if (!count($materials = $object->getMaterials()))
            throw new Exception\InvalidArgumentException('Skip data was not saved.');

        $this->furnaceLogDbRepository->beginTransaction();

        try {

            $productWeight = 0;
            /** @var ArrayObject $material */
            foreach ($materials as $material) {

                $currentSkip = clone $object;
                $currentSkip->setMaterials(null);
                $currentSkip->setProviderId($material->offsetGet('provider_id'));
                $currentSkip->setMaterialId($material->offsetGet('material_id'));
                $currentSkip->setWeight($material->offsetGet('weight'));
                $currentSkip->setDropout($material->offsetGet('dropout'));

                $this->furnaceLogDbRepository->loadingTransaction($currentSkip);

                $warehouseMaterialLog = new WarehouseLogEntity();
                $warehouseMaterialLog->setContractorId($currentSkip->getProviderId());
                $warehouseMaterialLog->setComment('Загрузка сырья в печь');
                $warehouseMaterialLog->setCreated($currentSkip->getDate());
                $warehouseMaterialLog->setResourceId($currentSkip->getMaterialId());
                $warehouseMaterialLog->setWarehouseId($currentSkip->getMaterialWarehouseId());


                if ($currentSkip->getDropout()) {
                    $dropout = $currentSkip->getWeight() * $currentSkip->getDropout() / 100;
                    $weight = $currentSkip->getWeight() - $dropout;
                    $productWeight += 0.56 * $weight;
                    $warehouseMaterialLog->setResourceWeight($currentSkip->getWeight() + $dropout);
                } else {
                    $warehouseMaterialLog->setResourceWeight($currentSkip->getWeight());
                }

                $this->warehouseLogManager->output($warehouseMaterialLog);
            }

            $warehouseProductLog = new WarehouseLogEntity();
            $warehouseProductLog->setWarehouseId($object->getProductWarehouseId());
            $warehouseProductLog->setResourceWeight($productWeight);
            $warehouseProductLog->setComment('Выгрузка готовой продукции из печи');
            $warehouseProductLog->setCreated($object->getDate());

            $this->warehouseLogManager->input($warehouseProductLog);

            $this->furnaceLogDbRepository->commit();
        } catch (Exception\ErrorException $exception) {
            $this->furnaceLogDbRepository->rollback();
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'The products were successfully produced');
    }

}