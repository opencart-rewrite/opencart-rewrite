<?php

/**
 * Represent the request data (either from post or get)
 * we inherit from ArrayObject, so that old code that was doing
 * $data[] will still work
 */
Class RequestData extends ArrayObject
{
    private $_data;

    /**
     *  
     */
    public function __construct(array $data)
    {
        $this->_data = is_array($data)
            ? $data
            : array();
        parent::__construct($this->_data);
    }

    /**
     *  
     */
    public function set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Get value stored at index "key" or return $default
     * value otheriwse
     *
     * @param string $key     The index to look for
     * @param mixed  $default value to use in case the key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!isset($this->_data[$key])) {
            return $default;
        }

        return $this->_data[$key];
    }
}
