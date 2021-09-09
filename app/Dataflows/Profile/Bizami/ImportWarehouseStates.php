<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Dataflows\Profile\Bizami;

use App\Models\Bizami;
use App\Models\Dataflows\DataSource;
use App\Models\FtpConnection;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportProducts
 * @package App\Dataflows\Profile\Bizami
 */
class ImportWarehouseStates extends \App\Models\Dataflows\Profile
{
    /**
     * @var int
     */
    protected $counter = 0;
    /**
     * @var
     */
    protected $warehouses;
    /**
     * @var
     */
    protected $ftpConnection;

    /**
     *
     */
    public function execute()
    {
        $dirPath = $this->getParam('directory_path');
        $filesOnFtp = $this->getFtpConnection()->getFileList($dirPath, $this->getParam('file_pattern'));

        if (empty($filesOnFtp)) {
            $this->setNothingToDoStatus();
            $this->setResult('No files to import.');
            return;
        }

        $downloadDir = $this->getDownloadDirPath(true);
        $lastFile = array_pop($filesOnFtp);

        foreach ($filesOnFtp as $file) {
            $result = $this->moveToProcessed($file, 'MISSED');
            if (!$result) {
                $this->addCriticalLog($this->getFtpConnection()->getLastError());
                return;
            }
        }

        /** donwloading last file  */
        $this->addInfoLog('Downloading file: '. $lastFile);
        $fileToImport = $downloadDir . DIRECTORY_SEPARATOR . 'last_file.csv';

        if ($this->getFtpConnection()->downloadFile($lastFile, $fileToImport)) {
            $this->addInfoLog('File Downloaded.');
        } else {
            throw new \Exception('Unable to download file: ' . $lastFile);
        }

        Bizami\WarehouseState::query()->truncate();
        $this->importFile($fileToImport);
        $this->setResult('Imported ' . $this->counter . ' of warehouse states.');
        $this->moveToProcessed($lastFile);
    }

    /**
     * @return array[]
     */
    public function getParametersFormConfig()
    {
        return [
            'directory_path' => [
                'label' => 'Directory Path',
            ],
            'file_pattern' => [
                'label' => 'File Pattern',
            ],
            'should_move_to_processed' => [
                'label' => 'Should Move to processed',
            ],
        ];
    }

    /**
     * @param $filePathToImport
     * @throws \Exception
     */
    protected function importFile($filePathToImport)
    {
        $fields = [
            'warehouse_id',
            'product_id',
            'qty',
            'qty_reserved',
            'qty_ordered',
            'status',
            'norm',
        ];

        $this->addInfoLog('Start Import File:' . $filePathToImport);
        $csvSource = new DataSource\Csv();
        $csvSource->setHasHeaderLine(true);
        $csvSource->setDelimiter(',');
        $csvSource->setFileEncoding('UTF-8');
        $csvSource->setFile($filePathToImport);
        $csvSource->setFieldsMap($fields);
        $dataReader = $this->getDataReader($csvSource);

        while ($data = $dataReader->getData()) {
            $warehouseState = new Bizami\WarehouseState();
            $dataFieldList = $warehouseState->getDataFieldList();

            foreach ($dataFieldList as $fieldCode) {
                if ($fieldCode == 'id') {
                    continue;
                }

                $value = $data->getData($fieldCode);
                $warehouseState->$fieldCode = $value;
            }

            $warehouseState->qty = (int)$warehouseState->qty;
            $warehouseState->qty_reserved = (int)$warehouseState->qty_reserved;
            $warehouseState->qty_ordered = (int)$warehouseState->qty_reserved;
            $warehouseState->norm = (int)$warehouseState->norm;

            $warehouseState->save();
            $this->addWarehouse($data);

            $this->counter ++;
            if ($this->counter % 100 == 0) {
                $this->addInfoLog('Imported ' . $this->counter . ' of warehouse states.');
            }
        }
    }

    /**
     * @return FtpConnection
     */
    protected function getFtpConnection()
    {
        if ($this->ftpConnection == null) {
            $this->ftpConnection = new FtpConnection();
            $this->ftpConnection->setHost('sftp.alekseon-test.eu');
            $this->ftpConnection->setUsername('bizami');
            $this->ftpConnection->setPassword('DwyT6LpfqyZ87pkH');
            $this->ftpConnection->setPort(2022);
            $this->ftpConnection->setSsl();

        }
        return $this->ftpConnection;
    }

    /**
     * @param $file
     */
    protected function moveToProcessed($file, $processedDirName = 'PROCESSED')
    {
        if ($this->getParam('should_move_to_processed')) {
            $pathParts = explode('/', $file);
            $fileName = array_pop($pathParts);
            $dirPath = implode('/', $pathParts);
            $processedDir = $dirPath . '/' . $processedDirName . '/';
            $moveResult = $this->getFtpConnection()->moveFile($file, $processedDir . $fileName, true);
            return $moveResult;
        } else {
            return true;
        }
    }

    /**
     * @param false $createDir
     * @return string
     */
    protected function getDownloadDirPath($createDir = false)
    {
        $downloadDir = Storage::disk('bizami')->path('warehouse_states');
        if ($createDir) {
            Storage::disk('bizami')->makeDirectory('warehouse_states');
        }
        return $downloadDir;
    }

    /**
     * @param $data
     */
    public function addWarehouse($data)
    {
        $warehouseId = $data->getData('warehouse_id');
        if (!$warehouseId) {
            return;
        }

        if (is_null($this->warehouses)) {
            $warehouses = Bizami\Warehouse::all();
            foreach ($warehouses as $warehouse) {
                $this->warehouses[$warehouse->warehouse_id] = $warehouse;
            }
        }

        if (!isset($this->warehouses[$warehouseId])) {
            $warehouse = new Bizami\Warehouse();
            $warehouse->warehouse_id = $warehouseId;

            try {
                $warehouse->save();
                $this->warehouses[$warehouseId] = $warehouse;
            } catch (\Exception $e) {
                $this->addWarningLog($e->getMessage());
            }
        }
    }
}
