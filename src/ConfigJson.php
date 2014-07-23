<?php

namespace UmnLib\Core\Config;

class ConfigJson
{
    protected $_jsonFile = '';
    public function _jsonFile()
    {
        return $this->_jsonFile;
    }

    protected $_jsonString = '';
    public function _jsonString()
    {
        return $this->_jsonString;
    }

    protected $_requiredProperties = array();
    public function _requiredProperties()
    {
        return $this->_requiredProperties;
    }

    public function __construct( $params )
    {
        if (isset($params['_jsonString'])) {
            $_jsonString = $params['_jsonString'];
        } else if (isset($params['_jsonFile'])) {
            $_jsonFile = $params['_jsonFile'];
            $_jsonString = file_get_contents( $_jsonFile );
            if ($_jsonString == null) {
                throw new \InvalidArgumentException("Could not open config file '$_jsonFile'");
            }
            $this->_jsonFile = $_jsonFile;
        } else {
            throw new \InvalidArgumentException(
                'Params must contain either "_jsonString" or "_jsonFile"'
            );
        }
        $this->_jsonString = $_jsonString;
        $config_object = json_decode($_jsonString); 

        $properties = get_object_vars( $config_object );
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }

        if (!isset($params['_requiredProperties'])) return;

        $_requiredProperties = $params['_requiredProperties'];
        $this->_requiredProperties = $_requiredProperties;

        foreach($_requiredProperties as $property) {
            $property = trim($property);
            if (isset($this->$property)) continue;
            if (preg_match('/=/', $property)) {
                list($property, $value) = preg_split('/\s*=\s*/', $property);
                if (isset($property) && isset($value)) {
                    $this->$property = $value;
                    continue;
                }
            }
            throw new \InvalidArgumentException("Missing required property '$property'");
        }
    }
}
