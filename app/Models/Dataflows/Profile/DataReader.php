<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows\Profile;

use App\Models\Dataflows\DataSource\DataSourceInterface;

/**
 * Class DataReader
 * @package App\Models\Dataflows\Profile
 */
class DataReader
{
    protected $iterator = 0;
    protected $dataSource;
    protected $dataPack;

    /**
     * @param $dataSource
     * @return $this
     * @throws \Exception
     */
    public function setDataSource($dataSource)
    {
        if ($this->dataSource === null) {
            if (!$dataSource instanceof DataSourceInterface) {
                throw new \Exception('Please define a correct data instance.');
            }
            $this->dataSource = $dataSource;
        }
        return $this;
    }

    /**
     * @return null
     * @throws \Exception
     */
    protected function getDataPack()
    {
        if (!$this->dataSource) {
            throw new \Exception('No source data.');
        }

        if ($this->dataPack) {
            if (count($this->dataPack) <= $this->iterator) {
                $this->dataPack = null;
                $this->iterator = 0;
            }
        }
        if ($this->dataPack === null) {
            $this->dataPack = $this->dataSource->getDataPack();
        }
        return $this->dataPack;
    }

    /**
     * @return false
     */
    public function getData()
    {
        $dataPack = $this->getDataPack();
        if ($dataPack) {
            if (isset($dataPack[$this->iterator])) {
                $data = $dataPack[$this->iterator];
                $this->iterator++;
                return $data;
            }
        }
        return false;
    }
}
