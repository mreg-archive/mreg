<?php
namespace mreg;


class DispatchTest extends \PHPUnit_Framework_TestCase
{

    function testGetters()
    {
        $settings = new Settings;
        $route = new \mreg\tests\RouteMock;
        
        $map = $this->getMock(
            '\Aura\Router\Map',
            array(),
            array(),
            '',
            FALSE
        );
        
        $request = new \itbz\httpio\Request;
        
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE
        );
        
        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array(),
            array(),
            '',
            FALSE
        );
        
        $dispatch = new Dispatch(
            $settings,
            $route,
            $map,
            $request,
            $session,
            $user
        );
    
        $this->assertSame($settings, $dispatch->getSettings());
        $this->assertSame($route, $dispatch->getRoute());
        $this->assertSame($map, $dispatch->getMap());
        $this->assertSame($request, $dispatch->getRequest());
        $this->assertSame($session, $dispatch->getSession());
        $this->assertSame($user, $dispatch->getUser());
    }

}
