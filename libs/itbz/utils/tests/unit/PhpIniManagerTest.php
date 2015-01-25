<?php
namespace itbz\utils;

class PhpIniManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        ini_set('serialize_precision', 17);
        ini_set('precision', 14);
    }

    public function testVerifySuccess()
    {
        $ini = new PhpIniManager();
        $settings = array(
            'serialize_precision' => 17,
            'precision' => 14
        );
        $this->assertTrue($ini->verify($settings));
        $this->assertSame(0, count($ini->getErrors()));
    }

    public function testVerifyFailure()
    {
        $ini = new PhpIniManager();
        $settings = array(
            'serialize_precision' => 18,
            'precision' => 14
        );
        $this->assertFalse($ini->verify($settings));
        $this->assertSame(1, count($ini->getErrors()));
    }

    public function testApplySuccess()
    {
        $ini = new PhpIniManager();
        $settings = array(
            'serialize_precision' => 18,
            'precision' => 14
        );
        $this->assertTrue($ini->apply($settings));
        $this->assertSame(0, count($ini->getErrors()));
    }

    public function testApplyFailure()
    {
        $ini = new PhpIniManager();
        $settings = array(
            'disable_functions' => 'randon,list,of,functions,to,disable,foo,bar'
        );
        $this->assertFalse($ini->apply($settings));
        $this->assertSame(1, count($ini->getErrors()));
    }

    public function testVerifyFile()
    {
        $ini = new PhpIniManager();
        $fname = __DIR__ . "/data/phpIniManagerTest.ini";
        $this->assertTrue($ini->verifyFile($fname));
    }

    public function testApplyFile()
    {
        $ini = new PhpIniManager();
        $fname = __DIR__ . "/data/phpIniManagerTest.ini";
        $this->assertTrue($ini->applyFile($fname));
    }

    /**
     * @expectedException itbz\utils\Exception
     */
    public function testFileNotReadable()
    {
        $ini = new PhpIniManager();
        $fname = __DIR__ . "/data/phpIniManagerTest.ini.does.not.exist";
        $ini->applyFile($fname);
    }

    /**
     * @expectedException itbz\utils\Exception
     */
    public function testInvalidIniFile()
    {
        $ini = new PhpIniManager();
        $fname = __DIR__ . "/data/phpIniManagerTest.invalid.ini";
        $ini->applyFile($fname);
    }
}
