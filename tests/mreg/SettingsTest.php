<?php
namespace mreg;


class SettingsTest extends \PHPUnit_Framework_TestCase
{

    function testExistsSetRemove()
    {
        $s = new Settings();
        $this->assertFalse($s->exists('foo'));
        
        $s->set('foo', TRUE);
        $this->assertTrue($s->exists('foo'));
        
        $s->remove('foo');
        $this->assertFalse($s->exists('foo'));
    }


    function testSetGet()
    {
        $s = new Settings();
        $s->set('foo', 'bar');
        $this->assertEquals('bar', $s->get('foo'));
    }


    /**
     * @expectedException mreg\Exception
     */
    function testGetError()
    {
        $s = new Settings();
        $s->get('foo');
    }


    function testGetAll()
    {
        $s = new Settings();
        $s->set('foo', 'foo');
        $s->set('bar', 'bar');
        
        $expected = array(
            'foo' => 'foo',
            'bar' => 'bar',
        );
        
        $this->assertEquals($expected, $s->getAll());
    }


    function testSetBulk()
    {
        $s = new Settings();

        $bulk = array(
            'foo' => 'foo',
            'bar' => 'bar',
        );

        $s->setBulk($bulk);
        $this->assertEquals($bulk, $s->getAll());
    }


    function testClear()
    {
        $s = new Settings();
 
        $s->set('foo', 'foo');
        $this->assertTrue($s->exists('foo'));
        
        $s->clear();
        $this->assertFalse($s->exists('foo'));
    }


    function testExportImport()
    {
        $s = new Settings();

        $bulk = array(
            'foo' => 'foo',
            'bar' => 'bar',
        );

        $s->setBulk($bulk);

        $str = serialize($s);
        unset($s);
        $s1 = unserialize($str);
        
        $this->assertEquals($bulk, $s1->getAll());
    }

}
