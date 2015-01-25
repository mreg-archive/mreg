<?php
namespace mreg\Auth;


class SessionTest extends \PHPUnit_Framework_TestCase
{

    function getPdo()
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $session = new Session($pdo, 'sessions', FALSE);
        $session->createDbTable();

        return $pdo;
    }

    function testUserName()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setUser('foo');
        $sessionA->close();
        
        $sessionB = new Session($pdo, 'sessions');   
        $this->assertEquals('foo', $sessionB->getUser());
        $sessionB->close();
        $this->assertEquals($sessionA->getId(), $sessionB->getId());
    }

    function testRegenerate()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setError('foo');
        $sessionA->regenerate();
        $sessionA->close();

        $sessionB = new Session($pdo, 'sessions');   
        $this->assertEquals('foo', $sessionB->getError());
        $sessionB->close();
        $this->assertEquals($sessionA->getId(), $sessionB->getId());
    }

    function testSetUserError()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setUser('foo');
        $sessionA->close();

        $sessionA->setUserError('foo', 'error');

        $sessionB = new Session($pdo, 'sessions');   
        $this->assertEquals('error', $sessionB->getError());
        $sessionB->close();
    }

    function testDestroy()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setUser('foo');
        $sessionA->close();

        $sessionAA = new Session($pdo, 'sessions');
        $sessionAA->destroy();

        $sessionB = new Session($pdo, 'sessions');   
        $this->assertEquals('', $sessionB->getUser());
        $sessionB->close();
    }

    function testGetActiveUsers()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setId('A');
        $sessionA->setUser('foo');
        $sessionA->close();

        $sessionB = new Session($pdo, 'sessions');   
        $sessionB->setId('B');
        $sessionB->setUser('bar');
        $sessionB->close();
        
        $this->assertEquals(array('foo', 'bar'), $sessionB->getActiveUsers());
    }

    function testGarbageCollection()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->setUser('foo');
        $sessionA->close();

        $sessionA->collectGarbage(-1);        

        $sessionB = new Session($pdo, 'sessions');   
        $this->assertEquals('', $sessionB->getUser());
        $sessionB->close();
    }

    function testSessionVars()
    {
        $pdo = $this->getPdo();
        $sessionA = new Session($pdo, 'sessions');   
        $sessionA->set('foo', 'bar');
        $sessionA->close();

        $sessionB = new Session($pdo, 'sessions');   
        $this->assertTrue($sessionB->has('foo'));
        $this->assertEquals('bar', $sessionB->get('foo'));
        $sessionB->close();
    }

    /**
     * @expectedException mreg\Exception
     */
    function testNoSessionVarError()
    {
        $pdo = $this->getPdo();
        $sessionB = new Session($pdo, 'sessions');   
        $this->assertFalse($sessionB->has('foo'));
        $sessionB->get('foo');
        $sessionB->close();
    }

}
