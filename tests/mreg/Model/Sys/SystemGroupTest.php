<?php
namespace mreg\Model\Sys;
use itbz\httpio\Request;


class SystemGroupTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \mreg\Exception\InvalidContentException
     */
    function testSetNameError()
    {
        $sysgroup = new SystemGroup();
        $sysgroup->setName('1234567890123');
    }


    function testImportExport()
    {
        $sysgroup = new SystemGroup();

        $data = array(
            'name' => 'test',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'description' => 'foobar'
        );

        $sysgroup->load($data);

        $this->assertTrue(is_array($sysgroup->export()));
    }


    function testLoadRequest()
    {
        $sysgroup = new SystemGroup();

        $data = array(
            'name' => 'test',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'description' => 'foobar'
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $sysgroup->loadRequest($request);

        $this->assertTrue(is_array($sysgroup->extract(1, array())));
    }

}
