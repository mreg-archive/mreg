<?php
namespace itbz\phplibaddress\Composer;

use itbz\phpcountry\Country;
use itbz\phplibaddress\Address;

class SvTest extends \PHPUnit_Framework_TestCase
{
    public function getAddress()
    {
        $country = new Country;
        $country->setLang('en');
        $addr = new Address($country);

        return $addr;
    }

    /**
     * @expectedException \itbz\phplibaddress\Exception
     */
    public function testGetAddressException()
    {
        $sv = new Sv(new Breviator);
        $sv->getAddress();
    }

    public function testGetAddress()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);
        $this->assertSame($a, $sv->getAddress());
    }

    public function testClone()
    {
        $address = $this->getAddress();
        $sv = new Sv(new Breviator, $address);
        $clone = clone $sv;

        $address->setGivenName('foobar');

        $this->assertFalse(
            $sv->getAddressee() == $clone->getAddressee(),
            'Clone should not referense the same object'
        );
    }

    public function testIsValid()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $a->setThoroughfare('Very very very long street name 12345');
        $this->assertFalse($sv->isValid());

        $a->setThoroughfare('Very very very long street name 1234');
        $this->assertTrue($sv->isValid());

        $a->setNameOfMailee('Foo Bar');
        $a->setPostcode('12345');
        $a->setTown('xtown');
        $a->setGivenName('Hannes');
        $a->setOrganisationalUnit('Unit');
        $a->setOrganisationName('Itbrigaden');
        $a->setCountryCode('us');
        $this->assertFalse($sv->isValid());
    }

    public function testGetValid()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $a->setThoroughfare('Very very very long street name 12345');
        $this->assertEquals(
            'Very very very long street name 1234',
            $sv->getValid()
        );

        $a->setNameOfMailee('Foo Bar');
        $a->setPostcode('12345');
        $a->setTown('xtown');
        $a->setGivenName('Hannes');
        $a->setOrganisationalUnit('Unit');
        $a->setOrganisationName('Itbrigaden');
        $a->setCountryCode('us');

        $this->assertEquals(
            "Hannes\nItbrigaden\nc/o Foo Bar\nVery very very long street name 1234\nUS-12345 xtown\nUnited States",
            $sv->getValid()
        );
    }

    public function testGetAddressee()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals('', $sv->getAddressee());

        $a->setForm('Mr');
        $a->setGivenName('Karl Hannes Gustav');
        $a->setSurname('Forsgard');
        $a->setOrganisationalUnit('Unit');
        $this->assertEquals(
            "Unit\nMr Karl Hannes Gustav Forsgard",
            $sv->getAddressee()
        );

        $a->setOrganisationName('Itbrigaden');
        $this->assertEquals(
            "Unit\nMr Karl Hannes Gustav Forsgard\nItbrigaden",
            $sv->getAddressee()
        );

        $a->setLegalStatus('AB');
        $this->assertEquals(
            "Unit\nMr Karl Hannes Gustav Forsgard\nItbrigaden AB",
            $sv->getAddressee()
        );

        $a->setOrganisationName('Itbrigaden 1234567890123456789012345');
        $this->assertEquals(
            "Unit\nMr Karl Hannes Gustav Forsgard\nItbrigaden 1234567890123456789012345",
            $sv->getAddressee()
        );
    }

    public function testGetMailee()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals($sv->getMailee(), '');

        $a->setNameOfMailee('Foo Bar');
        $this->assertEquals('c/o Foo Bar', $sv->getMailee());

        $a->setMaileeRoleDescriptor('bar');
        $this->assertEquals('bar Foo Bar', $sv->getMailee());
    }

    public function testGetServicePoint()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals($sv->getServicePoint(), '');

        $a->setDeliveryService('Poste restante');
        $this->assertEquals('Poste restante', $sv->getServicePoint());

        $a->setDeliveryService('Box');
        $a->setAlternateDeliveryService('123');
        $this->assertEquals('Box 123', $sv->getServicePoint());
    }

    public function testGetDeliveryLocation()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals('', $sv->getDeliveryLocation());

        $a->setThoroughfare('Yostreet');
        $this->assertEquals('Yostreet', $sv->getDeliveryLocation());

        $a->setPlot('1');
        $this->assertEquals('Yostreet 1', $sv->getDeliveryLocation());

        $a->setLittera('A');
        $this->assertEquals('Yostreet 1 A', $sv->getDeliveryLocation());

        $a->setStairwell('UH');
        $this->assertEquals('Yostreet 1 A UH', $sv->getDeliveryLocation());

        $a->setFloor('2tr');
        $this->assertEquals('Yostreet 1 A UH 2tr', $sv->getDeliveryLocation());

        $a->setDoor('11');
        $this->assertEquals(
            'Yostreet 1 A UH lgh 11',
            $sv->getDeliveryLocation()
        );

        $a->setSupplementaryData('Across A street');
        $this->assertEquals(
            "Across A street\nYostreet 1 A UH lgh 11",
            $sv->getDeliveryLocation()
        );

        $a->setThoroughfare('Very very very long street name');
        $this->assertEquals(
            "Very very very long street name\n1 A UH lgh 11",
            $sv->getDeliveryLocation()
        );
    }

    public function testGetLocality()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals('', $sv->getLocality());

        $a->setTown('xtown');
        $this->assertEquals('xtown', $sv->getLocality());

        $a->setPostcode('12345');
        $this->assertEquals('12345 xtown', $sv->getLocality());

        $a->setCountryCode('us');
        $this->assertEquals("US-12345 xtown\nUnited States", $sv->getLocality());

        $a->setCountryOfOrigin('SE');
        $this->assertEquals("US-12345 xtown\nUnited States", $sv->getLocality());

        $a->setCountryOfOrigin('US');
        $this->assertEquals("12345 xtown", $sv->getLocality());
    }

    public function testGetDeliveryPoint()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals('', $sv->getDeliveryPoint());

        $a->setPostcode('12345');
        $a->setTown('xtown');
        $this->assertEquals('12345 xtown', $sv->getDeliveryPoint());

        $a->setThoroughfare('Yostreet');
        $a->setPlot('1');
        $this->assertEquals("Yostreet 1\n12345 xtown", $sv->getDeliveryPoint());

        $a->setDeliveryService('Box');
        $a->setAlternateDeliveryService('123');
        $this->assertEquals("Box 123\n12345 xtown", $sv->getDeliveryPoint());
    }

    public function testFormat()
    {
        $a = $this->getAddress();
        $sv = new Sv(new Breviator, $a);

        $this->assertEquals('', $sv->format());

        $a->setNameOfMailee('Foo Bar');
        $this->assertEquals('c/o Foo Bar', $sv->format());

        $a->setThoroughfare('Yostreet');
        $a->setPlot('1');
        $a->setPostcode('12345');
        $a->setTown('xtown');
        $this->assertEquals(
            "c/o Foo Bar\nYostreet 1\n12345 xtown",
            $sv->format()
        );

        $a->setGivenName('Hannes');
        $a->setSurname('Forsgard');
        $this->assertEquals(
            "Hannes Forsgard\nc/o Foo Bar\nYostreet 1\n12345 xtown",
            $sv->format()
        );

        $a->setOrganisationalUnit('Unit');
        $a->setOrganisationName('Itbrigaden');
        $this->assertEquals(
            "Unit\nHannes Forsgard\nItbrigaden\nc/o Foo Bar\nYostreet 1\n12345 xtown",
            $sv->format()
        );

        $a->setCountryCode('us');
        $this->assertEquals(
            "Unit\nHannes Forsgard\nItbrigaden\nc/o Foo Bar\nYostreet 1\nUS-12345 xtown\nUnited States",
            $sv->format()
        );
    }
}
