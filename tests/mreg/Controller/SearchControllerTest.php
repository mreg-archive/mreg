<?php
namespace mreg\Controller;


class SearchControllerTest extends \PHPUnit_Framework_TestCase
{

    function testSearchDefaultPage()
    {
        $request = new \itbz\httpio\Request(
            '',
            '',
            'GET',
            array(),
            array(),
            array('q' => 'foobar')
        );

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->once())
            ->method('generate')
            ->with('search')
            ->will($this->returnValue('/search'));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getMap', 'getRequest'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->once())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $pdo = $this->getMock(
            '\mreg\Controller\MockPDO',
            array('prepare')
        );

        $stmt = $this->getMock(
            'PDOStatement',
            array('fetch')
        );

        $stmt->expects($this->atLeastOnce())
             ->method('fetch')
             ->will($this->onConsecutiveCalls(array('name'=>'foobar'), NULL));

        $pdo->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($stmt));

        $controller = new SearchController($pdo);
        $response = $controller->search($dispatch);
        $this->assertInstanceOf('\itbz\httpio\Response', $response);
        
        $content = json_decode($response->getContent());
        $this->assertTrue(isset($content->title));
        $this->assertTrue(isset($content->links));
        $this->assertTrue(isset($content->items));
    }


    function testPaging()
    {
        $request = new \itbz\httpio\Request(
            '',
            '',
            'GET',
            array(),
            array(),
            array(
                'q' => 'foobar',
                'startPage' => '2',
                'itemsPerPage' => '1'
            )
        );

        $map = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            FALSE
        );

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getMap', 'getRequest'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->once())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $pdo = $this->getMock(
            '\mreg\Controller\MockPDO',
            array('prepare')
        );

        $stmt = $this->getMock(
            'PDOStatement',
            array('fetch')
        );

        $stmt->expects($this->atLeastOnce())
             ->method('fetch')
             ->will($this->returnValue(array('name'=>'foobar')));

        $pdo->expects($this->once())
            ->method('prepare')
            ->will($this->returnValue($stmt));

        $controller = new SearchController($pdo);
        $response = $controller->search($dispatch);
        $this->assertInstanceOf('\itbz\httpio\Response', $response);
        
        $content = json_decode($response->getContent());
        $this->assertTrue(isset($content->links));
        $this->assertTrue(isset($content->links->prev));
        $this->assertTrue(isset($content->links->next));
    }

}

class MockPDO extends \PDO
{
    function __construct(){}
}
