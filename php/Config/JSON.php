<?php

class Config_JSON
{
    protected $_json_file = '';
    public function _json_file()
    {
        return $this->_json_file;
    }

    protected $_json_string = '';
    public function _json_string()
    {
        return $this->_json_string;
    }

    protected $_required_properties = array();
    public function _required_properties()
    {
        return $this->_required_properties;
    }

    public function __construct( $params )
    {
        if (isset($params['_json_string'])) {
            $_json_string = $params['_json_string'];
        } else if (isset($params['_json_file'])) {
            $_json_file = $params['_json_file'];
            $_json_string = file_get_contents( $_json_file );
            if ($_json_string == null) {
                throw new Exception("Could not open config file '$_json_file'");
            }
            $this->_json_file = $_json_file;
        } else {
            throw new Exception(
                'Params must contain either "_json_string" or "_json_file"'
            );
        }
        $this->_json_string = $_json_string;
        $config_object = json_decode($_json_string); 

        $properties = get_object_vars( $config_object );
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }

        if (!isset($params['_required_properties'])) return;

        $_required_properties = $params['_required_properties'];
        $this->_required_properties = $_required_properties;

        foreach($_required_properties as $property) {
            $property = trim($property);
            if (isset($this->$property)) continue;
            if (preg_match('/=/', $property)) {
                list($property, $value) = preg_split('/\s*=\s*/', $property);
                if (isset($property) && isset($value)) {
                    $this->$property = $value;
                    continue;
                }
            }
            throw new Exception("Missing required property '$property'");
        }
        //print_r( $this );
    }

} // end class Config_JSON

?>
