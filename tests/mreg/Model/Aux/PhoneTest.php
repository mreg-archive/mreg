<?php
namespace mreg\Model\Aux;
use itbz\httpio\Request;
use itbz\phplibphone\Number;
use itbz\phplibphone\Library\Countries;
use itbz\phpcountry\Country as PhpCountry;


class PhoneTest extends \PHPUnit_Framework_TestCase
{

    function getPhone()
    {
        $phpCountry = new PhpCountry;
        $phpCountry->setLang('sv');
        $countryLib = new Countries($phpCountry);

        return new Phone(new Number($countryLib));
    }


    function testClone()
    {
        $phone = $this->getPhone();

        $clone = clone $phone;
        $phone->load(array(
            'cc' => '45',
            'ndc' => '8',
            'sn' => '1234567',
        ));
        
        $this->assertFalse(
            $phone->getNr() == $clone->getNr(),
            'Parsers should not reference the same object'
        );
    }

    function testImportExport()
    {
        $phone = $this->getPhone();

        $phone->load(array(
            'cc' => '45',
            'ndc' => '8',
            'sn' => '1234567',
            'carrier' => '?'
        ));
        
        $export = $phone->export('');
        $this->assertTrue($export['valid']);
        $this->assertEquals('+45 8 123 45 67', $export['nr']);
    }


    function testLoadRequest()
    {
        $data = array(
            'name' => 'testname',
            'nr' => '81234567',
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $phone = $this->getPhone();
        $phone->loadRequest($request);

        $export = $phone->export('');

        $this->assertTrue(array_key_exists('area', $export));
        $this->assertTrue(array_key_exists('country', $export));
        $this->assertTrue(array_key_exists('mobile', $export));
        $this->assertTrue(array_key_exists('valid', $export));
        $this->assertTrue(array_key_exists('nr', $export));
        $this->assertTrue(array_key_exists('carrier', $export));
    }


    function testExtract()
    {
        $phone = $this->getPhone();

        $phone->load(array(
            'cc' => '45',
            'ndc' => '8',
            'sn' => '1234567',
            'carrier' => '?'
        ));
        
        $extract = $phone->extract(1, array());
        
        $this->assertTrue(array_key_exists('carrier', $extract));
        $this->assertTrue(array_key_exists('sn', $extract));
        $this->assertTrue(array_key_exists('ndc', $extract));
        $this->assertTrue(array_key_exists('cc', $extract));

        $this->assertFalse(array_key_exists('area', $extract));
        $this->assertFalse(array_key_exists('country', $extract));
        $this->assertFalse(array_key_exists('mobile', $extract));
        $this->assertFalse(array_key_exists('valid', $extract));
        $this->assertFalse(array_key_exists('nr', $extract));
    }

}
