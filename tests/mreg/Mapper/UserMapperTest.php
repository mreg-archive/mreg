<?php
namespace mreg\Mapper;

use mreg\Model\Sys\User;
use mreg\Auth\Password;
use Phpass\Hash;
use Phpass\Strength;
use Phpass\Strength\Adapter\Wolfram;

class UserMapperTest extends \PHPUnit_Framework_TestCase
{

    function getUser()
    {
        $hasher = new Hash;
        $strength = new Strength(new Wolfram);
        $pswd = new Password($hasher, $strength);

        return new User($pswd); 
    }


    function testExtractForCreate()
    {
        $mapperStub = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array('extractArray'),
            array(),
            '',
            FALSE
        );

        $mapperStub->expects($this->once())
            ->method('extractArray')
            ->will($this->returnValue(array()));

        $expressionSet = $mapperStub->extractForCreate($this->getUser());
        $group = $expressionSet
            ->getExpression(\mreg\Mapper\UserMapper::GROUP_FIELD)
            ->getValue();

        $this->assertEquals(
            'user-edit',
            $group,
            'Group of created users should always be user-edit'
        );
    }


    function testTickInvalidAuth()
    {
        $mapperStub = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array('update'),
            array(),
            '',
            FALSE
        );

        // On tickInvalidAuth user should be written to db
        $mapperStub->expects($this->once())
            ->method('update');

        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('setModifiedBy', 'incrementInvalidAuths'),
            array(),
            '',
            FALSE
        );

        // Invalid-auths-counter should be incremented
        $user->expects($this->once())
            ->method('incrementInvalidAuths');

        // Modified-by should be set to sys
        $user->expects($this->once())
            ->method('setModifiedBy')
            ->with('sys');

        $mapperStub->tickInvalidAuth($user);
    }


    function testTickValidAuth()
    {
        $mapperStub = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array('update'),
            array(),
            '',
            FALSE
        );

        // On tickInvaAuth user should be written to db
        $mapperStub->expects($this->once())
            ->method('update');
    
        $mapperStub->tickValidAuth($this->getUser());  
    }


    /**
     * Create a mock mapper using in all tests of update() (see below)
     */
    function getMapperForUpdateTests()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array('getPrimaryKey', 'update'),
            array(),
            '',
            FALSE
        );

        $stmt = $this->getMock(
            'PDOStatement'
        );

        $table->expects($this->any())
            ->method('update')
            ->will($this->returnValue($stmt));

        $mapper = $this->getMock(
            '\mreg\Mapper\UserMapper',
            array('extractForUpdate', 'extractForRead'),
            array($table, ($this->getUser()))
        );

        $mapper->expects($this->any())
            ->method('extractForUpdate')
            ->will($this->returnValue(
                new \itbz\datamapper\pdo\ExpressionSet
            ));

        $mapper->expects($this->any())
            ->method('extractForRead')
            ->will($this->returnValue(
                new \itbz\datamapper\pdo\ExpressionSet
            ));

        return $mapper;
    }


    function testNoUpdateForRoot()
    {
        $user = $this->getUser();
        $user->setName('root');

        $mapper = $this->getMapperForUpdateTests();
        $mapper->update($user);
    }


    function testBlockAfterInvalidAuths()
    {
        $user = $this->getUser();
        $user->setInvalidAuths(4);

        $mapper = $this->getMapperForUpdateTests();
        $mapper->setBlockAfterFailures(3);

        $mapper->update($user);

        $this->assertEquals(
            User::STATUS_MULTIPLE_ERRORS,
            $user->getStatus()
        );
    }


    function testBlockAfterInactivity()
    {
        $user = $this->getUser();
        $user->setLoginBeforeLast('1');

        $mapper = $this->getMapperForUpdateTests();
        $mapper->setBlockAfterInactive(3);

        $mapper->update($user);

        $this->assertEquals(
            User::STATUS_INACTIVE,
            $user->getStatus()
        );
    }


    function testBlockOldPassword()
    {
        $user = $this->getUser();
        $user->setPswdCreated('1');

        $mapper = $this->getMapperForUpdateTests();
        $mapper->setPswdLifeSpan(3);

        $mapper->update($user);

        $this->assertEquals(
            User::STATUS_EXPIRED_PSWD,
            $user->getStatus()
        );
    }


    function testWarnOldPassword()
    {
        $twoMonthsAgo = strtotime("-70 day");
        $user = $this->getUser();
        $user->setPswdCreated($twoMonthsAgo);

        $mapper = $this->getMapperForUpdateTests();
        $mapper->setPswdLifeSpan(3);

        $mapper->update($user);

        $this->assertFalse(
            $mapper->getWarning() == '',
            'There should ba a password will expire warning'
        );
    }

}