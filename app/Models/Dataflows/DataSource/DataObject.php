<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Dataflows\DataSource;

/**
 * Class DataObject
 * @package App\Models\Dataflows\DataSource
 */
class DataObject
{
    /**
     * @var array
     */
    protected $_data = [];

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getData($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        }

        return '';
    }
}
