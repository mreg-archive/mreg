<?php
namespace mreg\Auth;


class AuthUserBuilderTest extends \PHPUnit_Framework_TestCase
{

    function tearDown()
    {
        session_write_close();
    }


    function getSession()
    {
        $pdo = new \PDO('sqlite::memory:');
        $session = new Session($pdo, 'sessions', FALSE);
        
        return $session;
    }


    function testFakeAuth()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->once())
            ->method('findByPk')
            ->with('username');

        $builder = new AuthUserBuilder($session, $mapper);
        $builder->enableFakeAuth('username')
            ->getUser();
    }


    function testNoDatabaseHit()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->throwException(
                new \itbz\datamapper\exception\DataNotFoundException
            ));

        $builder = new AuthUserBuilder($session, $mapper);
        $user = $builder->enableFakeAuth('username')
            ->getUser();

        $this->assertInstanceOf("\mreg\NullObject\AnonymousUser", $user);
    }


    function testAlreadyLoggedIn()
    {
        $session = $this->getSession();
        $session->setUser('username');
        $session->set('fp', 'fingerprint');
        $session->set('agent', hash('crc32', 'useragent'));

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->once())
            ->method('findByPk')
            ->with('username');

        $builder = new AuthUserBuilder($session, $mapper);

        $builder->disableFakeAuth()
            ->setFingerprint('fingerprint')
            ->setUserAgent('useragent')
            ->getUser();
    }


    function testErrorSession()
    {
        $session = $this->getSession();
        $session->setUser('test');
        $session->setError('error');
 
        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $builder = new AuthUserBuilder($session, $mapper);
        $user = $builder->getUser();

        $this->assertInstanceOf("\mreg\NullObject\AnonymousUser", $user);
        $this->assertEquals('error', $builder->getError());
    }


    function testFailedAuthentication()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('isValidPassword'),
            array(),
            '',
            FALSE
        );

        $user->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(FALSE));

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->returnValue($user));

        $builder = new AuthUserBuilder($session, $mapper);

        $anonUser = $builder->setAuthHeader("BASICX dGVzdDptcmVndGVzdDEhQQ==")
            ->getUser();

        $this->assertInstanceOf("\mreg\NullObject\AnonymousUser", $anonUser);
    }


    function testValidAuth()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array('regenerate', 'setUser', 'setError', 'set', 'setUserError'),
            array(),
            '',
            FALSE            
        );
        
        $session->expects($this->once())
                ->method('regenerate');

        $session->expects($this->once())
                ->method('setUser')
                ->with('test');

        $session->expects($this->once())
                ->method('setError')
                ->with('');

        $session->expects($this->exactly(2))
                ->method('set');

        $session->expects($this->once())
                ->method('setUserError')
                ->with('test');

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('isValidPassword'),
            array(),
            '',
            FALSE
        );

        $user->setName('test');

        $user->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(TRUE));

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->returnValue($user));

        $builder = new AuthUserBuilder($session, $mapper);

        $authUser = $builder->setAuthHeader("BASICX dGVzdDptcmVndGVzdDEhQQ==")
            ->setSingleSession(TRUE)
            ->getUser();

        $this->assertEquals('test', $authUser->getName());
        $this->assertFalse('' == $builder->getNewFingerprint());
    }


    function testUserError()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('isValidPassword'),
            array(),
            '',
            FALSE
        );

        $user->setStatus(1);

        $user->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(TRUE));

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->returnValue($user));
        
        $builder = new AuthUserBuilder($session, $mapper);

        $user = $builder->setAuthHeader("BASICX dGVzdDptcmVndGVzdDEhQQ==")
            ->getUser();

        $this->assertInstanceOf("\mreg\NullObject\AnonymousUser", $user);
        $this->assertTrue('' == $builder->getNewFingerprint());
        $this->assertFalse('' == $builder->getError());
    }


    function testAuthWithNonExistingUser()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array('regenerate', 'setUser', 'setError', 'set', 'setUserError'),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->throwException(
                new \itbz\datamapper\exception\DataNotFoundException
            ));
        
        $builder = new AuthUserBuilder($session, $mapper);

        $user = $builder->setAuthHeader("BASICX dGVzdDptcmVndGVzdDEhQQ==")
            ->getUser();

        $this->assertInstanceOf("\mreg\NullObject\AnonymousUser", $user);
    }


    function testUserWarning()
    {
        $session = $this->getMock(
            '\mreg\Auth\Session',
            array(),
            array(),
            '',
            FALSE            
        );

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('isValidPassword'),
            array(),
            '',
            FALSE
        );

        $user->expects($this->once())
            ->method('isValidPassword')
            ->will($this->returnValue(TRUE));

        $mapper->expects($this->once())
            ->method('findByPk')
            ->will($this->returnValue($user));

        $mapper->expects($this->once())
            ->method('getWarning')
            ->will($this->returnValue('Some kind of warning..'));
        
        $builder = new AuthUserBuilder($session, $mapper);

        $user = $builder->setAuthHeader("BASICX dGVzdDptcmVndGVzdDEhQQ==")
            ->getUser();

        $this->assertFalse('' == $builder->getError());
    }

}