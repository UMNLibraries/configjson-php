#!/usr/bin/php -q
<?php

require_once 'simpletest/autorun.php';
SimpleTest :: prefer(new TextReporter());
set_include_path('../php' . PATH_SEPARATOR . get_include_path());
require_once 'Config/JSON.php';

ini_set('memory_limit', '2G');

error_reporting( E_ALL );

class ConfigJSONTest extends UnitTestCase
{
    public function test_file()
    {
        $json_file = getcwd() . '/config.json';
        $required_properties = array(
           'firstName',
           'foo=bar', // required property with default value
        );
        $config = new Config_JSON(array(
           '_json_file' => $json_file,
           '_required_properties' => $required_properties,
        ));
        $this->assertIsA($config, 'Config_JSON');
        $this->assertEqual($config->_json_file(), $json_file);
        $this->assertEqual($config->_required_properties(), $required_properties);

        $this->assertEqual($config->foo, 'bar');
        $this->assertEqual($config->firstName, 'John');
        $this->assertIsA($config->address, 'stdClass');
        $this->assertEqual($config->address->state, 'NY');
    }

    public function test_no_params()
    {
        $this->expectException();
        $config = new Config_JSON(array());
    }

    public function test_missing_required()
    {
        $json_string = '{
            "foo": "bar",
            "baz": "luhrmann"
        }';
        $required_properties = array(
           'fu',
        );
        $this->expectException();
        $config = new Config_JSON(array(
           '_json_string' => $json_string,
           '_required_properties' => $required_properties,
        ));
    }

    public function test_string()
    {
        $json_string = '{
            "foo": "bar",
            "baz": "luhrmann"
        }';
        $required_properties = array(
           ' foo ', // whitespace should be ignored
           'fu = manchu', // required property with default value & whitespace
        );
        $config = new Config_JSON(array(
           '_json_string' => $json_string,
           '_required_properties' => $required_properties,
        ));
        $this->assertIsA($config, 'Config_JSON');
        $this->assertEqual($config->_json_string(), $json_string);
        $this->assertEqual($config->_required_properties(), $required_properties);

        $this->assertEqual($config->baz, 'luhrmann');
        $this->assertEqual($config->fu, 'manchu');
    }

} // end ConfigJSONTest
