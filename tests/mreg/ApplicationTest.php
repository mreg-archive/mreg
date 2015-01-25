<?php
namespace mreg;

use itbz\httpio\Request;
use itbz\httpio\Response;
use mreg\NullObject\AnonymousUser;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    function testUnauthenticated()
    {
        $c = new \Pimple();
        
        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $c['debugLogger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );
        
        $c['settings'] = new Settings();
        $c['settings']->set('auth.enableFakeAuth', TRUE);
        $c['settings']->set('auth.checkSingeSession', TRUE);
        
        $c['userBuilder'] = $this->getMock(
            '\mreg\Auth\AuthUserBuilder',
            array(
                'enableFakeAuth',
                'setUserAgent',
                'setSingleSession',
                'setAuthHeader',
                'setFingerprint',
                'getUser'
            ),
            array(),
            '',
            FALSE
        );
        
        $c['userBuilder']->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue(new AnonymousUser));
        
        $request = new Request(
            '127.0.0.1',
            '',
            'GET',
            array(
                'USER-AGENT' => 'phpunit',
                'Authorization' => ''
            ),
            array(),
            array(),
            array(
                'fingerprint' => ''
            )
        );
        
        $server = array(
            'REQUEST_METHOD' => 'GET',
            'SERVER_ADDR' => '127.0.0.1'
        );
        
        $app = new Application($c);
        $response = $app->dispatch($request, $server);
        
        $this->assertSame(401, $response->getStatus());
    }


    function testNoRouteFound()
    {
        $c = new \Pimple();
        
        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $c['settings'] = new Settings();
        $c['settings']->set('auth.enableFakeAuth', FALSE);
        $c['settings']->set('auth.checkSingeSession', FALSE);
        
        $c['userBuilder'] = $this->getMock(
            '\mreg\Auth\AuthUserBuilder',
            array(
                'enableFakeAuth',
                'setUserAgent',
                'setSingleSession',
                'setAuthHeader',
                'setFingerprint',
                'getUser'
            ),
            array(),
            '',
            FALSE
        );

        $c['userBuilder']->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($this->getMock(
                '\mreg\Model\Sys\User',
                array(),
                array(),
                '',
                FALSE
            )));
        
        $c['userMapper'] = $this->getMock(
            '\itbz\datamapper\pdo\access\AcMapper',
            array(),
            array(),
            '',
            FALSE
        );
        
        $c['routes'] = $this->getMock(
            'Aura\Router\Map',
            array(),
            array(),
            '',
            FALSE
        );
        
        $request = new Request(
            '',
            '',
            'GET',
            array(
                'USER-AGENT' => 'phpunit',
            )
        );
        
        $server = array(
            'REQUEST_METHOD' => 'GET',
            'SERVER_ADDR' => '127.0.0.1'
        );
        
        $app = new Application($c);
        $response = $app->dispatch($request, $server);
        $this->assertSame(404, $response->getStatus());
    }


    function testRouting()
    {
        $c = new \Pimple();
        
        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['pdo']->expects($this->atLeastOnce())
            ->method('inTransaction')
            ->will($this->returnValue(TRUE));

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $c['debugLogger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );
 
        $c['settings'] = new Settings();
        $c['settings']->set('auth.enableFakeAuth', FALSE);
        $c['settings']->set('auth.checkSingeSession', FALSE);
        
        $c['userBuilder'] = $this->getMock(
            '\mreg\Auth\AuthUserBuilder',
            array(
                'enableFakeAuth',
                'setUserAgent',
                'setSingleSession',
                'setAuthHeader',
                'setFingerprint',
                'getUser',
                'getNewFingerprint',
                'isError'
            ),
            array(),
            '',
            FALSE
        );

        $c['userBuilder']->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($this->getMock(
                '\mreg\Model\Sys\User',
                array(),
                array(),
                '',
                FALSE
            )));

        $c['userBuilder']->expects($this->once())
            ->method('getNewFingerprint')
            ->will($this->returnValue('fingerprint'));

        $c['userBuilder']->expects($this->once())
            ->method('isError')
            ->will($this->returnValue(TRUE));
        
        $c['routes'] = $this->getMock(
            'Aura\Router\Map',
            array('match'),
            array(),
            '',
            FALSE
        );

        $c['routes']->expects($this->once())
            ->method('match')
            ->will($this->returnValue(new \Aura\Router\Route('', '', array(), array(
                'controller' => 'controller',
                'action' => 'action'
            ))));
        
        $c['controller'] = new Controller();
        
        $c['session'] = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE
        );
        
        $request = new Request(
            '',
            '',
            'GET',
            array(
                'USER-AGENT' => 'phpunit',
            )
        );
        
        $server = array(
            'REQUEST_METHOD' => 'GET',
            'SERVER_ADDR' => '127.0.0.1'
        );
        
        $app = new Application($c);
        $response = $app->dispatch($request, $server);
        $this->assertSame(200, $response->getStatus());
    }


    function testAccessDeniedException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \itbz\datamapper\pdo\access\AccessDeniedException;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['pdo']->expects($this->atLeastOnce())
            ->method('inTransaction')
            ->will($this->returnValue(TRUE));

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(403, $response->getStatus());
    }


    function testDataNotFoundException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \itbz\datamapper\exception\DataNotFoundException;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(404, $response->getStatus());
    }

    
    function testDataMapperException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \itbz\datamapper\Exception;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testStbException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \itbz\stb\Exception;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testHttpioException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \itbz\httpio\Exception;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testPDOException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \PDOException;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testMregException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new Exception;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testConflictException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \mreg\Exception\HTTP\ConflictException;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(409, $response->getStatus());
    }


    function testOuterException()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            throw new \Exception;
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }


    function testError()
    {
        $c = new \Pimple();
    
        $c['settings'] = function(){
            // Trigger an error, should in the error handler of Application..
            trigger_error('testerror');
        };

        $c['pdo'] = $this->getMock('\mreg\MockPDO');

        $c['logger'] = $this->getMock(
            '\Monolog\Logger',
            array(),
            array('test')
        );

        $app = new Application($c);
        $response = $app->dispatch(new Request, array());
        $this->assertSame(500, $response->getStatus());
    }

}

class Controller
{
    function action()
    {
        return new Response();
    }
}

class MockPDO extends \PDO
{
    public function __construct(){}
}