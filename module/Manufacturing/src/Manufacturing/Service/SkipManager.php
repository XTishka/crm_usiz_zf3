<?php

namespace Manufacturing\Service;

use Application\Service\Result;
use DateTime;
use DateInterval;
use Manufacturing\Domain\SkipCommonEntity;
use Manufacturing\Domain\SkipMaterialEntity;
use Manufacturing\Domain\WarehouseLogEntity;
use Manufacturing\Exception\ErrorException;
use Manufacturing\Service\Repository;

class SkipManager {

    /**
     * @var Repository\DatabaseSkip
     */
    protected $databaseSkipRepository;

    /**
     * @var Repository\FurnaceDb
     */
    protected $furnaceDbRepository;

    /**
     * @var WarehouseLogManager
     */
    protected $warehouseLogManager;

    /**
     * SkipManager constructor.
     *
     * @param Repository\DatabaseSkip $databaseSkipRepository
     * @param Repository\FurnaceDb    $furnaceDbRepository
     * @param WarehouseLogManager     $warehouseLogManager
     */
    public function __construct(
        Repository\DatabaseSkip $databaseSkipRepository,
        Repository\FurnaceDb $furnaceDbRepository,
        WarehouseLogManager $warehouseLogManager
    ) {
        $this->databaseSkipRepository = $databaseSkipRepository;
        $this->furnaceDbRepository    = $furnaceDbRepository;
        $this->warehouseLogManager    = $warehouseLogManager;
    }


    public function getFurnaceDataByPlantId($plantId, $date = null) {
        if ($date) {
            $date = DateTime::createFromFormat('m.Y', $date);
        }
        return $this->databaseSkipRepository->fetchMaterialsData($plantId, $date);
    }

    /**
     * @param      $plantId
     * @param null $date
     *
     * @return array
     * @throws \Exception
     */
    public function getFurnaceLogByPlantId($plantId, $date = null) {

        $furnaces = array_filter($this->furnaceDbRepository->fetchFurnacesArray(), function ($furnace) use ($plantId) {
            return $furnace['plant_id'] == $plantId;
        });

        if ($date) {
            $date = DateTime::createFromFormat('d.m.Y', sprintf('01.%s', $date));
        }
        else {
            $date = new DateTime();
        }

        //$date = $date ? DateTime::createFromFormat('d.m.Y', $date) : new DateTime();

        $date->setDate($date->format('Y'), $date->format('m'), $date->format('t'));

        $thisDate = new DateTime();

        if ($date > $thisDate) {
            $date->setDate($date->format('Y'), $date->format('m'), $thisDate->format('d'));
        }
        $dateCurrent = ($date)->setTime(0, 0, 0, 0);
        $dateEnd     = (clone $dateCurrent)->setDate($dateCurrent->format('Y'), $dateCurrent->format('m'), 1);

        $generatedArray = [];
        foreach ($furnaces as $key => $furnace) {
            $generatedArray[$key + 1] = ['furnace_id' => $furnace['furnace_id'], 'status' => 'error'];
        }

        $data = [];

        while ($dateEnd <= $dateCurrent) {
            $dateCurrentClone                    = clone $dateCurrent;
            $data[$dateCurrent->format('Y-m-d')] = [$dateCurrentClone] + $generatedArray;
            $dateCurrent->sub(new DateInterval('P1D'));
        }

        $header = ['Load date'];
        foreach ($furnaces as $furnaceNumber => $option) {
            $furnaceNumber += 1;
            $header[]      = $option['furnace_name'];
            $skips         = $this->databaseSkipRepository->fetchSkipLogsArray($option['furnace_id'], $dateEnd);
            foreach ($skips as &$skip) {
                if (key_exists($skip['date'], $data) && key_exists($furnaceNumber, $data[$skip['date']])) {
                    $data[$skip['date']][$furnaceNumber] = [
                        'skip_id'         => $skip['skip_id'],
                        'furnace_id'      => $option['furnace_id'],
                        'status'          => 'success',
                        'weight_material' => $skip['weight_material'],
                        'weight_coal'     => $skip['weight_coal'],
                        'product_weight'  => $skip['product_weight'],
                        'dropout_weight'  => $skip['dropout_weight'],
                    ];
                }
                unset($skip);
            }
        }

        array_unshift($data, $header);

        return $data;

    }

    /**
     * @param $skipId
     *
     * @return SkipCommonEntity
     * @throws ErrorException
     */
    public function getOneSkipById($skipId) {
        $skipId = intval($skipId);
        return $this->databaseSkipRepository->fetchOneSkipById($skipId);
    }

    /**
     * @param SkipCommonEntity $object
     *
     * @return Result
     */
    public function saveSkip(SkipCommonEntity $object) {
        //echo '<h4>Данные загрузки</h4><pre>' . print_r($object, true) . '</pre>';
        $this->databaseSkipRepository->beginTransaction();
        try {

            if ($skipId = $object->getSkipId()) {
                $this->warehouseLogManager->deleteLogBySkipId($skipId);
            }

            $object = $this->databaseSkipRepository->saveSkip($object);

            $productWeight = 0;

            /** @var SkipMaterialEntity $material */
            foreach ($object->getMaterials() as $material) {
                $warehouseMaterialLog = new WarehouseLogEntity();
                $warehouseMaterialLog->setContractorId($material->getProviderId());
                $warehouseMaterialLog->setComment('Загрузка сырья в печь');
                $warehouseMaterialLog->setCreated($object->getDate());
                $warehouseMaterialLog->setResourceId($material->getMaterialId());
                $warehouseMaterialLog->setWarehouseId($object->getMaterialWarehouseId());
                $warehouseMaterialLog->setSkipId($object->getSkipId());

                if ($material->getDropout()) {


                    $productWeight += bcmul(0.58, $material->getWeight(), 4);

                    //$dropoutWeight =

                    //$materialTotalWeight = bcdiv(bcmul($material->getWeight() * 100, 4), (100 - $material->getDropout()), 4);
                    $materialTotalWeight = bcdiv(bcmul($material->getWeight(), 100, 4), (100 - $material->getDropout()), 4);

                    $warehouseMaterialLog->setResourceWeight($materialTotalWeight);
                }
                else {
                    $warehouseMaterialLog->setResourceWeight($material->getWeight());
                }

                //echo '<h4>Списываем со склада</h4><pre style="border-bottom: 1px solid red">' . print_r($warehouseMaterialLog, true) . '</pre>';

                $this->warehouseLogManager->output($warehouseMaterialLog);

            }

            $warehouseProductLog = new WarehouseLogEntity();
            $warehouseProductLog->setWarehouseId($object->getProductWarehouseId());
            $warehouseProductLog->setResourceWeight($productWeight);
            $warehouseProductLog->setComment('Выгрузка готовой продукции из печи');
            $warehouseProductLog->setCreated($object->getDate());
            $warehouseProductLog->setSkipId($object->getSkipId());

            //echo '<h4>Готовая продукция</h4><pre style="border-bottom: 1px solid red">' . print_r($warehouseProductLog, true) . '</pre>';

            $this->warehouseLogManager->input($warehouseProductLog);

            $this->databaseSkipRepository->commit();
        } catch (ErrorException $exception) {
            $this->databaseSkipRepository->rollback();
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Skip data was successfully saved.');
    }

    public function deleteSkip($skipId) {
        $this->databaseSkipRepository->beginTransaction();
        try {
            $this->warehouseLogManager->deleteLogBySkipId($skipId);
            $this->databaseSkipRepository->deleteSkip($skipId);
            $this->databaseSkipRepository->commit();
        } catch (ErrorException $exception) {
            $this->databaseSkipRepository->rollback();
            return new Result('error', $exception->getMessage());
        }
        return new Result('success', 'Skip transaction was successfully deleted.');
    }

}