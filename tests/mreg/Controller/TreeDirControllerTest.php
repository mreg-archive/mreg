<?php
namespace mreg\Controller;
use itbz\httpio\Request;
use mreg\NullObject\AnonymousUser;


class TreeDirControllerTest extends \PHPUnit_Framework_TestCase
{

    function testClearCacheOnUpdate()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $treefactory = $this->getMock(
            '\mreg\Tree\FactionTreeFactory'
        );

        $cache = $this->getMock(
            '\itbz\Cache\VoidCacher',
            array('remove')
        );

        $cacheKey = "key";

        // This is the test, cache should ne cleared
        $cache->expects($this->once())
              ->method('remove')
              ->with($cacheKey);

        $controller = $this->getMock(
            '\mreg\Controller\TreeDirController',
            array('getMainEntity'),
            array($mapper, $treefactory, $cache, $cacheKey)
        );

        $faction = $this->getMock(
            '\mreg\Model\Dir\Faction',
            array('loadRequest')
        );

        $controller->expects($this->once())
                   ->method('getMainEntity')
                   ->will($this->returnValue($faction));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('matchEtag')
        );

        $request->expects($this->once())
                ->method('matchEtag')
                ->will($this->returnValue(TRUE));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->mainUpdate($dispatch);
        $this->assertInstanceOf('\itbz\httpio\Response', $response);
    }


    function testClearCacheOnDelete()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $treefactory = $this->getMock(
            '\mreg\Tree\FactionTreeFactory'
        );

        $cache = $this->getMock(
            '\itbz\Cache\VoidCacher',
            array('remove')
        );

        $cacheKey = "key";

        // This is the test, cache should ne cleared
        $cache->expects($this->once())
              ->method('remove')
              ->with($cacheKey);

        $controller = $this->getMock(
            '\mreg\Controller\TreeDirController',
            array('getMainEntity'),
            array($mapper, $treefactory, $cache, $cacheKey)
        );

        $faction = $this->getMock(
            '\mreg\Model\Dir\Faction',
            array('loadRequest')
        );

        $controller->expects($this->once())
                   ->method('getMainEntity')
                   ->will($this->returnValue($faction));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('matchEtag')
        );

        $request->expects($this->once())
                ->method('matchEtag')
                ->will($this->returnValue(TRUE));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->once())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->mainDelete($dispatch);
        $this->assertInstanceOf('\itbz\httpio\Response', $response);
    }


    function testClearCacheOnCreate()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'getNewModel',
                'save',
                'getLastInsertId',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $faction = $this->getMock(
            '\mreg\Model\Dir\Faction',
            array('loadRequest')
        );

        $mapper->expects($this->once())
               ->method('getNewModel')
               ->will($this->returnValue($faction));

        $treefactory = $this->getMock(
            '\mreg\Tree\FactionTreeFactory'
        );

        $cache = $this->getMock(
            '\itbz\Cache\VoidCacher',
            array('remove')
        );

        $cacheKey = "key";

        // This is the test, cache should ne cleared
        $cache->expects($this->once())
              ->method('remove')
              ->with($cacheKey);

        $controller = $this->getMock(
            '\mreg\Controller\TreeDirController',
            array('getMainEntity'),
            array($mapper, $treefactory, $cache, $cacheKey)
        );

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('url'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array(),
            array('', '', 'GET', array(), array(), array(), array('name'=>'x'))
        );

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->once())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->once())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->mainCreate($dispatch);
        $this->assertInstanceOf('\itbz\httpio\Response', $response);
    }


    function testTreepathOnRead()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findManyAssociated', 'setAuthUser', 'findRevisions'),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->atLeastOnce())
               ->method('findManyAssociated')
               ->will($this->returnValue(array()));

        $treefactory = $this->getMock(
            '\mreg\Tree\FactionTreeFactory',
            array('createTree')
        );

        $tree = array(
            1 => array(1, array(2), array('link' => 'url')),
            2 => array(1, array(), '')
        );
        
        $treefactory->expects($this->once())
                    ->method('createTree')
                    ->will($this->returnValue($tree));


        $cache = $this->getMock(
            '\itbz\Cache\VoidCacher'
        );

        $cacheKey = "key";

        $controller = $this->getMock(
            '\mreg\Controller\TreeDirController',
            array('getMainEntity'),
            array($mapper, $treefactory, $cache, $cacheKey)
        );

        $faction = new \mreg\Model\Dir\Faction;
        $faction->setId(2);
        $faction->setEtag('');

        $controller->expects($this->once())
                   ->method('getMainEntity')
                   ->will($this->returnValue($faction));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue('url'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array(),
            array('', '', 'GET', array(), array(), array(), array('name'=>'x'))
        );

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->mainRead($dispatch);
        $data = json_decode($response->getContent());
        $this->assertSame('url', $data->links->up);
    }


    function testReadNodeTreeFromCache()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findManyAssociated', 'setAuthUser', 'findRevisions'),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->atLeastOnce())
               ->method('findManyAssociated')
               ->will($this->returnValue(array()));

        $treefactory = $this->getMock(
            '\mreg\Tree\FactionTreeFactory'
        );

        $tree = array(
            1 => array(1, array(2), array('link' => 'url')),
            2 => array(1, array(), '')
        );
        
        $cache = $this->getMock(
            '\itbz\Cache\VoidCacher',
            array('has', 'get')
        );

        $cache->expects($this->once())
              ->method('has')
              ->will($this->returnValue(TRUE));

        $cache->expects($this->once())
              ->method('get')
              ->will($this->returnValue($tree));

        $cacheKey = "key";

        $controller = $this->getMock(
            '\mreg\Controller\TreeDirController',
            array('getMainEntity'),
            array($mapper, $treefactory, $cache, $cacheKey)
        );

        $faction = new \mreg\Model\Dir\Faction;
        $faction->setId(2);
        $faction->setEtag('');

        $controller->expects($this->once())
                   ->method('getMainEntity')
                   ->will($this->returnValue($faction));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue('url'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array(),
            array('', '', 'GET', array(), array(), array(), array('name'=>'x'))
        );

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->mainRead($dispatch);
        $data = json_decode($response->getContent());
        $this->assertSame('url', $data->links->up);
    }

}
