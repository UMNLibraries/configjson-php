<?php

namespace UmnLib\Core\Tests;

use UmnLib\Core\Config\ConfigJson;

class ConfigJsonTest extends \PHPUnit_Framework_TestCase
{
    public function testFile()
    {
        $jsonFile = dirname(__FILE__) . '/fixtures/config.json';
        $requiredProperties = array(
           'firstName',
           'foo=bar', // required property with default value
        );
        $config = new ConfigJson(array(
           '_jsonFile' => $jsonFile,
           '_requiredProperties' => $requiredProperties,
        ));
        $this->assertInstanceOf('\UmnLib\Core\Config\ConfigJson', $config);
        $this->assertEquals($jsonFile, $config->_jsonFile());
        $this->assertEquals($requiredProperties, $config->_requiredProperties());

        $this->assertEquals('bar', $config->foo);
        $this->assertEquals('John', $config->firstName);
        $this->assertInstanceOf('\stdClass', $config->address);
        $this->assertEquals('NY', $config->address->state);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoParams()
    {
        $config = new ConfigJson(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMalformed()
    {
      $config = new ConfigJson(array(
        '_jsonFile' => dirname(__FILE__) . '/fixtures/malformed-config.json',
      ));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingRequired()
    {
        $jsonString = '{
            "foo": "bar",
            "baz": "luhrmann"
        }';
        $requiredProperties = array(
           'fu',
        );
        $config = new ConfigJson(array(
           '_jsonString' => $jsonString,
           '_requiredProperties' => $requiredProperties,
        ));
    }

    public function testString()
    {
        $jsonString = '{
            "foo": "bar",
            "baz": "luhrmann"
        }';
        $requiredProperties = array(
           ' foo ', // whitespace should be ignored
           'fu = manchu', // required property with default value & whitespace
        );
        $config = new ConfigJson(array(
           '_jsonString' => $jsonString,
           '_requiredProperties' => $requiredProperties,
        ));
        $this->assertInstanceOf('\UmnLib\Core\Config\ConfigJson', $config);
        $this->assertEquals($jsonString, $config->_jsonString());
        $this->assertEquals($requiredProperties, $config->_requiredProperties());
        $this->assertEquals('luhrmann', $config->baz);
        $this->assertEquals('manchu', $config->fu);
    }
}
