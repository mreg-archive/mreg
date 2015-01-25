<?php
namespace mreg\Model\Dir;

use itbz\httpio\Request;
use itbz\stb\Banking\PlusGiro;
use itbz\stb\Banking\Bankgiro;

class FactionTest extends \PHPUnit_Framework_TestCase
{

    function testClone()
    {
        $faction = new Faction();
        $faction->setPlusgiro('12-5');
        $clone = clone $faction;
        $this->assertTrue($faction->getPLusgiro() !== $clone->getPlusgiro());
    }


    function testSetEmptyPlusgiro()
    {
        $faction = new Faction();
        $faction->setPlusgiro('');
        $this->assertEquals('', (string)$faction->getPlusgiro());
    }


    function testSetEmptyBankgiro()
    {
        $faction = new Faction();
        $faction->setBankgiro('');
        $this->assertEquals('', (string)$faction->getBankgiro());
    }


    function testMetadata()
    {
        $faction = new Faction();
        $faction->setEtag('qwerty');
        $faction->setName('foobar');

        $this->assertEquals('qwerty', $faction->getEtag());
        $this->assertEquals('foobar', $faction->getTitle());
    }


    function testGetAddressee()
    {
        $faction = new Faction();

        $expected = array(
            'organisation_name' => ''
        );
        $this->assertEquals($expected, $faction->getAddressee());

        $faction->setName('foobar');
        $expected['organisation_name'] = 'foobar';
        $this->assertEquals($expected, $faction->getAddressee());
    }


    function testImportExport()
    {
        $faction = new Faction();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'parentId' => '1',
            'name' => 'foobar',
            'type' => 'LS',
            'accountantId' => '1',
            'plusgiro' => '12-5',
            'bankgiro' => '123-1232',
            'url' => 'http://url',
            'avatar' => 'http://avatar',
        );

        $faction->load($data);

        $expected = array(
            'id' => '2',
            'tCreated' => '1970-01-01T01:00:01+01:00',
            'tModified' => '1970-01-01T01:00:01+01:00',
            'type' => 'Faction',
            'title' => 'foobar',
            'name' => 'foobar',
            'type' => 'type_faction_LS',
            'description' => 'foobar',
            'notes' => '',
            'accountantId' => '1',
            'plusgiro' => '12-5',
            'bankgiro' => '123-1232',
            'url' => 'http://url',
            'avatar' => 'http://avatar',
            'parentId' => '1'
        );

        $this->assertEquals($expected, $faction->export());
        
        $this->assertEquals('1', $faction->getCreated()->getTimestamp());
        $this->assertEquals('1', $faction->getModified()->getTimestamp());
    }


    function testLoadRequest()
    {
        $data = array(
            'parentId' => '1',
            'name' => 'foobar',
            'description' => 'bla',
            'notes' => 'more bla',
            'url' => 'http://url',
            'avatar' => 'http://avatar',
            'plusgiro' => '',
            'bankgiro' => '',
            'type' => 'LS'
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $faction = new Faction();
        $faction->loadRequest($request);

        $expected = array(
            "tCreated" => 'Datum saknas',
            "tModified" => 'Datum saknas',
            'type' => 'Faction',
            'title' => 'foobar',
            "name" => "foobar",
            "type" => "type_faction_LS",
            "description" => "bla",
            "notes" => "more bla",
            "accountantId" => NULL,
            "plusgiro" => "",
            "bankgiro" => "",
            "url" => "http://url",
            "avatar" => "http://avatar",
            "parentId" => '1'
        );

        $this->assertEquals($expected, $faction->export());
    }

    
    function testExtract()
    {
        $faction = new Faction();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'parentId' => '1',
            'name' => 'foobar',
            'type' => 'LS',
            'description' => 'bla',
            'notes' => '',
            'accountantId' => '1',
            'plusgiro' => '12-5',
            'bankgiro' => '123-1232',
            'url' => 'http://url',
            'avatar' => 'http://avatar',
        );

        $faction->load($data);

        $expected = array(
            'id' => '2',
            'tCreated' => 1,
            'tModified' => 1,
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'parentId' => '1',
            'name' => 'foobar',
            'type' => 'LS',
            'description' => 'bla',
            'notes' => '',
            'accountantId' => '1',
            'plusgiro' => new PlusGiro('12-5'),
            'bankgiro' => new Bankgiro('123-1232'),
            'url' => 'http://url',
            'avatar' => 'http://avatar',
        );

        $this->assertEquals($expected, $faction->extract(1, array()));
    }

}
