<?php
namespace itbz\Cache;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $cache;

    abstract public function createCache();

    public function setUp()
    {
        $this->createCache();
    }

    public function testHas()
    {
        $this->cache->clear();
        $this->assertTrue(!$this->cache->has('foo'));
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->has('foo'));
    }

    public function testSetGet()
    {
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->get('foo') == 'bar');
    }

    public function testClear()
    {
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->has('foo'));
        $this->cache->clear();
        $this->assertTrue(!$this->cache->has('foo'));
    }

    public function testRemove()
    {
        $this->cache->set('foo', 'bar');
        $this->assertTrue($this->cache->has('foo'));
        $this->cache->remove('foo');
        $this->assertTrue(!$this->cache->has('foo'));
    }
}
