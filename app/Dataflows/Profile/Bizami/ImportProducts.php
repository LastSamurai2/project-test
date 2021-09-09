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
class ImportProducts extends \App\Models\Dataflows\Profile
{
    /**
     * @var int
     */
    protected $newProductsCounter = 0;
    /**
     * @var array
     */
    protected $allProductIds = [];
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

        Bizami\Product::query()->truncate();
        $this->importFile($fileToImport);
        $this->setResult('Imported ' . $this->newProductsCounter . ' of products.');
        $this->moveToProcessed($lastFile);
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
     * @param string $processedDirName
     * @return bool|mixed
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
        $downloadDir = Storage::disk('bizami')->path('products');
        if ($createDir) {
            Storage::disk('bizami')->makeDirectory('products');
        }
        return $downloadDir;
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
     */
    protected function importFile($filePathToImport)
    {
        $fields = [
            'sku',
            'name',
            'catalog_number',
            'provider',
            'producent',
            'catalog_group',
            'gross_weight',
            'gross_volume',
            'category', // akcyjny
            'is_active',
            'value',
        ];

        $this->addInfoLog('Start Import File:' . $filePathToImport);
        $csvSource = new DataSource\Csv();
        $csvSource->setHasHeaderLine(true);
        $csvSource->setDelimiter(',');
        $csvSource->setFileEncoding('UTF-8');
        $csvSource->setFile($filePathToImport);
        $csvSource->setFieldsMap($fields);
        $dataReader = $this->getDataReader($csvSource);
        $productsToInsert = [];
        $productModel = new Bizami\Product();

        $readLinesCounter = 0;

        while ($data = $dataReader->getData()) {
            $productData = [];
            $sku = $data->getData('sku');
            $skuNormalized  = $this->getNormalizedSku($sku);

            if (isset($this->allProductIds[$skuNormalized])) {
                $this->addCriticalLog('Invlid SKU "' . $sku . '". There is already SKU: "' . $this->allProductIds[$skuNormalized] . '"');
                continue;
            }

            $dataFieldList = $productModel->getDataFieldList();

            foreach ($dataFieldList as $fieldCode) {
                if ($fieldCode == 'id') {
                    continue;
                }

                $value = $data->getData($fieldCode);
                $productData[$fieldCode] = $value;
            }

            $productData['gross_weight'] = $productData['gross_weight'] ? (float) $productData['gross_weight'] : null;
            $productData['gross_volume'] = $productData['gross_volume'] ? (float) $productData['gross_volume'] : null;
            $productData['value'] = (float) $productData['value'];

            if ($productData['is_active'] == 'Akt') {
                $productData['is_active'] = 1;
            } else {
                $productData['is_active'] = 0;
            }

            $productsToInsert[] = $productData;
            $this->allProductIds[$skuNormalized] = $sku;

            $readLinesCounter ++;

            if ($readLinesCounter % 1000 == 0) {
                $this->insertProducts($productsToInsert);
            }
        }

        if (!empty($productsToInsert)) {
            $this->insertProducts($productsToInsert);
        }
    }

    /**
     * @param $productsToInsert
     * @throws \Exception
     */
    protected function insertProducts(&$productsToInsert)
    {
        try {
            Bizami\Product::insert($productsToInsert);
            $this->newProductsCounter += count($productsToInsert);
        } catch (\Exception $e) {
            foreach ($productsToInsert as $productData) {
                try {
                    Bizami\Product::insert([$productData]);
                    $this->newProductsCounter ++;
                } catch (\Exception $e) {
                    $this->addCriticalLog($e->getMessage());
                }
            }
        }

        $this->addInfoLog('Imported ' . $this->newProductsCounter . ' of products.');
        $productsToInsert = [];
    }

    /**
     * @param $sku
     * @return string
     */
    protected function getNormalizedSku($sku)
    {
        // stripAccents
        return str_replace(
            ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż'],
            ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z'],
            $sku
        );
    }
}
