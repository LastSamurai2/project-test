<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows\DataSource;

/**
 * Class Csv
 * @package App\Models\Dataflows\DataSource
 */
class Csv implements DataSourceInterface
{
    const MAX_LINES_IN_ONE_DATAPACK = 100;

    protected $file;
    protected $fileHandle;
    protected $dataPackCounter = 0;
    protected $hasHeaderLine = false;
    protected $headers = [];
    protected $encoding = null;
    protected $useHeaderAsFieldMap = false;
    protected $lineLength = 0;
    protected $fieldsMap = [];
    protected $delimiter = ',';
    protected $enclosure = '"';

    /**
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        if ($this->fileHandle) {
            $this->closeFileHandle();
        }

        if ($this->file) {
            $this->fileHandle = null;
        }
        $this->file = $file;
        return $this;
    }

    /**
     * @param $fieldsMap
     * @return $this
     */
    public function setFieldsMap($fieldsMap)
    {
        $this->fieldsMap = $fieldsMap;
        return $this;
    }

    /**
     * @param $delimiter
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @param $enclosure
     * @return $this
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * @param $hasHeaderLine
     * @return $this
     */
    public function setHasHeaderLine($hasHeaderLine)
    {
        $this->hasHeaderLine = (bool) $hasHeaderLine;
        return $this;
    }

    /**
     * @param $useHeaderAsFieldMap
     * @return $this
     */
    public function setUseHeaderAsFieldMap($useHeaderAsFieldMap)
    {
        $this->useHeaderAsFieldMap = (bool) $useHeaderAsFieldMap;
        return $this;
    }

    /**
     * @param $encoding
     * @return $this
     */
    public function setFileEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return false|resource
     * @throws \Exception
     */
    protected function getFileHandle()
    {
        if ($this->fileHandle === null) {
            if (!file_exists($this->file)) {
                throw new \Exception('File "' . $this->file . '" does not exist');
            }
            $this->fileHandle = fopen($this->file, 'r');
        }
        return $this->fileHandle;
    }

    /**
     *
     */
    protected function closeFileHandle()
    {
        fclose($this->fileHandle);
        $this->fileHandle = false;
        return $this;
    }

    /**
     * @param $string
     * @param false $force
     * @return array|false|mixed|string|string[]|null
     */
    protected function encode($string, $force = false)
    {
        if ($this->encoding) {
            $string = mb_convert_encoding($string, 'UTF-8', $this->encoding);
        }
        return $string;
    }

    /**
     *
     */
    protected function createFieldsMapFromHeaders()
    {
        if (!empty($this->fieldsMap)) {
            return;
        }

        $fieldMap = [];
        foreach ($this->headers as $key => $header) {
            $fieldCode = preg_replace('/[^a-z0-9A-Z]+/i', '', $header);
            $fieldCode = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $fieldCode)), '_');
            $fieldMap[$key] = $fieldCode;
        }

        $this->setFieldsMap($fieldMap);
    }

    /**
     * @return array|false
     * @throws \Exception
     */
    public function getDataPack()
    {
        $fileHandle = $this->getFileHandle();
        if ($fileHandle) {
            $data = [];
            $counter = 0;
            while ($counter < self::MAX_LINES_IN_ONE_DATAPACK
                && $csvData = $this->fgetcsv($fileHandle)
            ) {
                $counter++;
                $rowNumber = $counter + $this->dataPackCounter * self::MAX_LINES_IN_ONE_DATAPACK;

                if ($this->hasHeaderLine && $rowNumber == 1) {
                    foreach ($csvData as $fieldNumber => $headerValue) {
                        $this->headers[$fieldNumber] = $this->encode($headerValue);
                    }
                    continue;
                }

                $dataObject = new DataObject\Csv();
                $dataObject->setRowNumber($rowNumber);
                $dataObject->setFilePath($this->file);
                if ($this->hasHeaderLine) {
                    $dataObject->setHeaders($this->headers);
                    if ($this->useHeaderAsFieldMap) {
                        $this->createFieldsMapFromHeaders();
                    }
                }
                $objectData = [];
                foreach ($csvData as $fieldNumber => $fieldValue) {
                    if (isset($this->fieldsMap[$fieldNumber])) {
                        $objectData[$this->fieldsMap[$fieldNumber]] = $fieldValue;
                    }
                    $objectData[DataObject\Csv::ROW_DATA_FIELD_CODE][$fieldNumber] = $fieldValue;
                }
                $dataObject->setData($objectData);
                $data[] = $dataObject;
            }
            $this->dataPackCounter++;
            if (empty($data)) {
                $this->closeFileHandle();
                return false;
            } else {
                return $data;
            }
        }
        return false;
    }

    /**
     * @param $stream
     * @return array|false
     */
    protected function fgetcsv($stream)
    {
        while (!feof($stream)) {
            $line = fgets($stream);

            if (trim($line) == '') { //ignore empty lines
                continue;
            }

            $line = $this->encode($line);
            return str_getcsv($line, $this->delimiter, $this->enclosure);
        }
        return false;
    }
}
