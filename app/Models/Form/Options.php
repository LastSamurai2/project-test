<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Form;

/**
 * Class Options
 * @package App\Models\Form
 */
class Options
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Options constructor.
     * @param array $optionsArray
     */
    public function __construct($optionsArray = [])
    {
        $this->options = $optionsArray;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptionsArray()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $optionsArray = [];

        foreach ($this->options as $code => $label) {
            $optionsArray[] = [
                'code' => $code,
                'label' => $label,
            ];
        }

        return $optionsArray;
    }
}
