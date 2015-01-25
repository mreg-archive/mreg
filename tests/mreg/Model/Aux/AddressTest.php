<?php
namespace mreg\Model\Aux;
use itbz\httpio\Request;
use itbz\phpcountry\Country;
use itbz\phplibaddress\Address as PhpLibAddress;
use itbz\phplibaddress\Composer\Sv;
use itbz\phplibaddress\Composer\Breviator;


class AddressTest extends \PHPUnit_Framework_TestCase
{

    function getAddress()
    {
        $country = new Country;
        $country->setLang('en');
        $addr = new PhpLibAddress($country);
        $addr->setCountryOfOrigin('SE');
        $composer = new Sv(new Breviator, $addr);

        return new Address($composer);
    }


    function testClone()
    {
        $address = $this->getAddress();
        $clone = clone $address;
        $address->load(array(
            'thoroughfare' => 'teststreet'
        ));
        
        $this->assertFalse(
            $address->getDeliveryPoint() == $clone->getDeliveryPoint(),
            'Parser should not referense the same object'
        );
    }


    function testImportExport()
    {
        $address = $this->getAddress();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'ref_table' => 'testtable',
            'ref_id' => 'testid',
            'name' => 'testname',

            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );

        $address->load($data);

        $expected = array(
            'id' => '2',
            'tCreated' => '1970-01-01T01:00:01+01:00',
            'tModified' => '1970-01-01T01:00:01+01:00',
            'name' => 'testname',
            'type' => 'type_address',
            'title' => 'testname',
            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );

        $this->assertEquals($expected, $address->export(''));
        $this->assertEquals('1', $address->getCreated()->getTimestamp());
        $this->assertEquals('1', $address->getModified()->getTimestamp());
        
        $expected = "teststreet 1 A\n12345 testtown";
        $this->assertEquals($expected, $address->getDeliveryPoint());
        
        $expected = "c/o testfriend\n" . $expected;
        $this->assertEquals($expected, $address->getAddress(array()));
        
        $this->assertTrue($address->equals($address));
    }

    
    function testLoadRequest()
    {
        $data = array(
            'name' => 'testname',
            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $address = $this->getAddress();
        $address->loadRequest($request);

        $expected = array(
            'tCreated' => 'Datum saknas',
            'tModified' => $address->getModified()->format(\DateTime::ATOM),
            'name' => 'testname',
            'type' => 'type_address',
            'title' => 'testname',
            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );

        $this->assertEquals($expected, $address->export(''));
    }


    function testExtract()
    {
        $address = $this->getAddress();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'ref_table' => 'testtable',
            'ref_id' => 'testid',
            'name' => 'testname',

            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );

        $address->load($data);

        $expected = array(
            'id' => '2',
            'tCreated' => 1,
            'tModified' => 1,
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'name' => 'testname',
            'ref_table' => 'testtable',
            'ref_id' => 'testid',
            'mailee_role_descriptor' => 'c/o',
            'mailee' => 'testfriend',
            'thoroughfare' => 'teststreet',
            'plot' => '1',
            'littera' => 'A',
            'stairwell' => '',
            'floor' => '',
            'door' => '',
            'supplementary_delivery_point_data' => '',
            'delivery_service' => '',
            'alternate_delivery_service' => '',
            'postcode' => '12345',
            'town' => 'testtown',
            'country_code' => 'SE',
        );

        $this->assertEquals($expected, $address->extract(1, array()));
    }

}
