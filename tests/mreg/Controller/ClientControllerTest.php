<?php
namespace mreg\Controller;
use itbz\httpio\Request;

class ClientControllerTest extends \PHPUnit_Framework_TestCase
{

    function testRedirect()
    {
        $controller = new ClientController();

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->once())
            ->method('generate')
            ->with('jsclient', array())
            ->will($this->returnValue('/root/'));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getRequest'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $response = $controller->bootstrap($dispatch);
        
        $this->assertEquals(
            '/root/jsclient/index.html',
            $response->getHeader('Location'),
            'Should redirect to jsclient'
        );
    }


    function testBootstrap()
    {
        $controller = new ClientController();

        $map = $this->getMock(
            'Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue('/root/'));

        // Create request with json accept header
        $request = new Request('', '', 'GET', array('Accept' => 'application/json'));

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('export'),
            array(),
            '',
            FALSE
        );

        $user->expects($this->once())
             ->method('export')
             ->will($this->returnValue(array()));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getMap', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue($user));

        $response = $controller->bootstrap($dispatch);
        
        $this->assertEquals(
            'application/json',
            $response->getHeader('Content-Type'),
            'Should send json data'
        );
    }


    function testLogout()
    {
        $controller = new ClientController();

        $session = $this->getMock(
            '\mreg\Auth\Session',
            array('destroy'),
            array(),
            '',
            FALSE
        );

        $session->expects($this->once())
                ->method('destroy');

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getSession'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->once())
                 ->method('getSession')
                 ->will($this->returnValue($session));
        
        $controller->logout($dispatch);
    }

}
