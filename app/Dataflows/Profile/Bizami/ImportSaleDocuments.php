<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Dataflows\Profile\Bizami;

use App\Models\Bizami;
use App\Models\Dataflows\DataSource;
use App\Models\FtpConnection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportSaleDocuments
 * @package App\Dataflows\Profile\Bizami
 */
class ImportSaleDocuments extends \App\Models\Dataflows\Profile
{
    /**
     * @var int
     */
    protected $newDocumentsCounter = 0;
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

        foreach ($filesOnFtp as $file) {
            $this->addInfoLog('Downloading file: '. $file);
            $fileToImport = $downloadDir . DIRECTORY_SEPARATOR . 'last_file.csv';

            if ($this->getFtpConnection()->downloadFile($file, $fileToImport)) {
                $this->addInfoLog('File Downloaded.');
            } else {
                throw new \Exception('Unable to download file: ' . $file);
            }

            $this->importFile($fileToImport, $file);
            $this->moveToProcessed($file);
        }

        $this->setResult('Imported ' . $this->newDocumentsCounter . ' of sale documents.');
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
    protected function importFile($filePathToImport, $sourceFileName)
    {
        $fields = [
            'document_id',
            'date',
            'type',
            'product_id',
            'qty',
            'value',
            'warehouse_id',
            'is_investment',
        ];

        $this->addInfoLog('Start Import File:' . $filePathToImport);
        $csvSource = new DataSource\Csv();
        $csvSource->setHasHeaderLine(true);
        $csvSource->setDelimiter(',');
        $csvSource->setFileEncoding('UTF-8');
        $csvSource->setFile($filePathToImport);
        $csvSource->setFieldsMap($fields);
        $dataReader = $this->getDataReader($csvSource);

        $documentsToInsert = [];
        $saleDocument = new Bizami\SaleDocument();
        $dataFieldList = $saleDocument->getDataFieldList();
        $from = Carbon::today()->sub(18, 'months')->toDateTimeString();

        $fileIsAlreadyImported = false;
        $alreadyImportedDocumentsCounter = $this->countImportedDocumentsFromFile($sourceFileName);
        if ($alreadyImportedDocumentsCounter > 0) {
            $this->addWarningLog('The file "' . $sourceFileName . '" was already imported earlier.');
            $fileIsAlreadyImported = true;
        }

        $readLinesCounter = 0;

        while ($data = $dataReader->getData()) {
            $saleDocumentData = [];

            $documentData = $data->getData('date');
            if ($documentData < $from) {
                continue; // we dont need older than 18 months documents for calculations
            }

            if ($fileIsAlreadyImported) {
                $readLinesCounter ++;
                continue;
            }

            foreach ($dataFieldList as $fieldCode) {
                if ($fieldCode == 'id') {
                    continue;
                }

                $value = $data->getData($fieldCode);
                $saleDocumentData[$fieldCode] = $value;
            }

            $saleDocumentData['source_file'] = $sourceFileName;
            $saleDocumentData['value'] = $this->stringToFloat($saleDocumentData['value']);
            $saleDocumentData['qty'] = (int) $saleDocumentData['qty'];

            switch ($saleDocumentData['is_investment']) {
                case 'Nie':
                    $saleDocumentData['is_investment'] = 0;
                    break;
                case 'Tak':
                    $saleDocumentData['is_investment'] = 1;
                    break;
                default:
                    $this->addWarningLog(
                        'Unknown value is investment '
                        . $saleDocumentData['is_investment']
                        .  ' for document: '
                        . $saleDocumentData['document_id']
                    );
                    $saleDocumentData['is_investment'] = 0;
            }

            $documentsToInsert[] = $saleDocumentData;

            $readLinesCounter ++;
            if ($readLinesCounter % 1000 == 0) {
                $this->insertDocuments($documentsToInsert);
            }
        }

        if (!empty($documentsToInsert)) {
            $this->insertDocuments($documentsToInsert);
        }

        if ($fileIsAlreadyImported && $readLinesCounter > $alreadyImportedDocumentsCounter) {
            $this->addCriticalLog(
                'There is '
                . $readLinesCounter
                . ' lines in file, but there were '
                . $alreadyImportedDocumentsCounter
                . ' lines imported before.'
            );
        }
    }

    /**
     * @param $documentsToInsert
     */
    protected function insertDocuments(&$documentsToInsert)
    {
        try {
            Bizami\SaleDocument::insert($documentsToInsert);
            $this->newDocumentsCounter += count($documentsToInsert);
        } catch (\Exception $e) {
            foreach ($documentsToInsert as $documentData) {
                try {
                    Bizami\SaleDocument::insert([$documentData]);
                    $this->newDocumentsCounter ++;
                } catch (\Exception $e) {
                    $this->addCriticalLog($e->getMessage());
                }
            }
        }

        $this->addInfoLog('Imported ' . $this->newDocumentsCounter . ' sale documents.');
        $documentsToInsert = [];
    }

    /**
     * @param $filename
     */
    protected function countImportedDocumentsFromFile($filename)
    {
        $saleDocument = Bizami\SaleDocument::all()->where('source_file', $filename);
        return $saleDocument->count();
    }

    /**
     * @return mixed
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
        $downloadDir = Storage::disk('bizami')->path('sales_documents');
        if ($createDir) {
            Storage::disk('bizami')->makeDirectory('sales_documents');
        }
        return $downloadDir;
    }

    /**
     * @param $string
     */
    protected function stringToFloat($string)
    {
        $string = str_replace(',', '.', $string);
        return (float) $string;
    }
}
