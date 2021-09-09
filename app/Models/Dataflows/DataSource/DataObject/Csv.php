<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows\DataSource\DataObject;

/**
 * Class Csv
 * @package App\Models\Dataflows\DataSource\DataObject
 */
class Csv extends \App\Models\Dataflows\DataSource\DataObject
{
    const ROW_DATA_FIELD_CODE = '_row_data';

    protected $rowNumber;
    protected $filePath;
    protected $headers;

    /**
     * @param $rowNumber
     * @return $this
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
        return $this;
    }

    /**
     * @param $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @param $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $headers;
    }

    /**
     * @param $fieldNumber
     */
    public function getRowData($fieldNumber)
    {
        $rowData = $this->getData(self::ROW_DATA_FIELD_CODE);
        if (is_array($rowData) && array_key_exists($fieldNumber, $rowData)) {
            return $rowData[$fieldNumber];
        }
        return null;
    }
}
