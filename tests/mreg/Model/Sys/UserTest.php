<?php
namespace mreg\Model\Sys;

use itbz\httpio\Request;
use mreg\Auth\Password;
use Phpass\Hash;
use Phpass\Strength;
use Phpass\Strength\Adapter\Wolfram;


class UserTest extends \PHPUnit_Framework_TestCase
{

    function getUser()
    {
        $hasher = new Hash;
        $strength = new Strength(new Wolfram);
        $pswd = new Password($hasher, $strength);
        $pswd->setStrongLimit(1);

        return new User($pswd); 
    }


    function testIncrementInvalidAuths()
    {
        $user = $this->getUser();

        $user->incrementInvalidAuths();
        $this->assertEquals(
            1,
            $user->getInvalidAuths(),
            'Starting from an unset value incrementing yields 1'
        );

        $user->incrementInvalidAuths();
        $this->assertEquals(
            2,
            $user->getInvalidAuths()
        );
    }


    function testIncrementLogins()
    {
        $user = $this->getUser();

        $user->incrementLogins();
        $this->assertEquals(
            1,
            $user->getLogins(),
            'Starting from an unset value incrementing yields 1'
        );

        $user->incrementLogins();
        $this->assertEquals(
            2,
            $user->getLogins()
        );
    }


    function testRotateLoginDates()
    {
        $user = $this->getUser();
        $user->rotateLoginDates();
        $mark = new \DateTime();

        $this->assertTrue(
            $mark >= $user->getLastLogin(),
            'last-login should be older than the mark'
        );

        $this->assertTrue(
            $mark >= $user->getLoginBeforeLast(),
            'login-befor-last should be older than the mark'
        );

        $oldLastLogin = $user->getLastLogin();
        $user->rotateLoginDates();

        $this->assertTrue(
            $oldLastLogin == $user->getLoginBeforeLast(),
            'login-befor-last should now be the old last-login value'
        );

        $this->assertTrue(
            $mark >= $user->getLoginBeforeLast(),
            'login-befor-last should still be older than the mark'
        );

        $this->assertTrue(
            $mark <= $user->getLastLogin(),
            'login-last should now be younger than the mark'
        );
    }


    function testGetAddressee()
    {
        $user = $this->getUser();

        $expected = array(
            'surname' => ''
        );
        
        $this->assertEquals($expected, $user->getAddressee());

        $user->setFullName('Bar');
        $expected = array(
            'surname' => 'Bar'
        );
        $this->assertEquals($expected, $user->getAddressee());
    }


    function testNullObjects()
    {
        $user = $this->getUser();
        $this->assertTrue(is_array($user->getGroups()));
        
        $this->assertInstanceOf(
            '\mreg\NullObject\NullDate',
            $user->getLastLogin()
        );
    }


    function testClone()
    {
        $user1 = $this->getUser();
        $user1->setLastLogin('1');
        $user1->setLoginBeforeLast('1');
        $login1 = $user1->getLastLogin();
        $user2 = clone $user1;
        $login2 = $user2->getLastLogin();
        $this->assertFalse($login1 === $login2);
    }


    function testImportExport()
    {
        $user = $this->getUser();

        $data = array(
            'uname' => 'test',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'fullname' => 'foobar',
            'password' => 'foobar',
            'tPswdCreated' => '1',
            'groups' => 'foobar',
            'status' => '0',
            'nInvalidAuths' => '0',
            'nLogins' => '0',
            'tLogin' => '1',
            'tLastLogin' => '1',
            'notes' => 'foobar',
            'accountingFor' => 'foobar'
        );

        $user->load($data);

        $this->assertTrue(is_array($user->export()));
    }


    function testLoadRequest()
    {
        $user = $this->getUser();

        $data = array(
            'uname' => 'test',
            'fullname' => 'foobar',
            'groups' => 'foobar',
            'status' => 'block',
            'notes' => 'foobar',
            'accountingFor' => '1',
            'newPswdA' => 'foobar',
            'newPswdB' => 'foobar'
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $user->loadRequest($request);

        $user->setLogins(2);
        $user->setInvalidAuths(2);

        $this->assertTrue(is_array($user->extract(1, array())));
    }


    /**
     * @expectedException \mreg\Exception
     */
    function testSetInvalidPassword()
    {
        $user = $this->getUser();

        $data = array(
            'uname' => 'test',
            'newPswdA' => 'foo',
            'newPswdB' => 'bar'
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $user->loadRequest($request);
    }


    function testActivateUser()
    {
        $user = $this->getUser();

        $data = array(
            'uname' => 'test',
            'status' => 'activate',
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $user->loadRequest($request);

        $this->assertEquals('', $user->getStatusDesc());
        $this->assertTrue($user->isActive());
    }


    function testIsValidPassword()
    {
        $pswd = $this->getMock(
            '\mreg\Auth\Password',
            array('isMatch'),
            array(),
            '',
            FALSE
        );

        $pswd->expects($this->once())
            ->method('isMatch')
            ->with('abc')
            ->will($this->returnValue(TRUE));

        $user = new User($pswd);

        $this->assertTrue($user->isValidPassword('abc'));
    }


    function testNullDate()
    {
        $user = $this->getUser();
        $this->assertInstanceOf(
            '\mreg\NullObject\NullDate',
            $user->getPswdCreated()
        );
    }

}