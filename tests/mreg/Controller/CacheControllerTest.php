<?php
namespace mreg\Controller;

class CacheControllerTest extends \PHPUnit_Framework_TestCase
{

    function testClearCache()
    {
        $cache = $this->getMock(
            '\itbz\Cache\CacheInterface',
            array('clear', 'has', 'get', 'set', 'remove')
        );

        $cache->expects($this->once())
              ->method('clear');

        $controller = new CacheController($cache);

        $response = $controller->clearCache();

        $this->assertInstanceOf('\itbz\httpio\Response', $response);
        $this->assertSame(204, $response->getStatus());
        $this->assertSame('', $response->getContent());
    }

}
