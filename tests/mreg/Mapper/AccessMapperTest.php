<?php
namespace mreg\Mapper;


class AccessMapperTest extends \PHPUnit_Framework_TestCase
{

    function testSetAuthUser()
    {
        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('foo'),
            array(),
            '',
            FALSE
        );

        $user->setName('foo');
        $user->setGroups(array('bar'));

        $mapperStub = $this->getMock(
            '\mreg\Mapper\AccessMapper',
            array('setUser'),
            array(),
            '',
            FALSE
        );

        $mapperStub->expects($this->once())
            ->method('setUser')
            ->with('foo', array('bar'));

        $mapperStub->setAuthUser($user);
    }


    function testGetAnonymousUser()
    {
        $mapperStub = $this->getMock(
            '\mreg\Mapper\AccessMapper',
            array('setUser'),
            array(),
            '',
            FALSE
        );

        $user = $mapperStub->getAuthUser();

        $this->assertInstanceOf(
            '\mreg\NullObject\AnonymousUser',
            $user,
            'When no user is set the null user should be returned'
        );
    }


    function testGetAuthUser()
    {
        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('foo'),
            array(),
            '',
            FALSE
        );

        $user->setName('foo');

        $mapperStub = $this->getMock(
            '\mreg\Mapper\AccessMapper',
            array('setUser'),
            array(),
            '',
            FALSE
        );

        $mapperStub->setAuthUser($user);

        $this->assertSame(
            $user,
            $mapperStub->getAuthUser()
        );
    }


    function testExtractForUpdate()
    {
        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('foo'),
            array(),
            '',
            FALSE
        );

        $user->setName('foo');

        $mapperStub = $this->getMock(
            '\mreg\Mapper\AccessMapper',
            array('extractArray', 'setUser'),
            array(),
            '',
            FALSE
        );

        $mapperStub->expects($this->once())
            ->method('extractArray')
            ->will($this->returnValue(array()));

        $mapperStub->setAuthUser($user);

        $expressionSet = $mapperStub->extractForUpdate($user);

        $this->assertEquals(
            'foo',
            $expressionSet->getExpression('modifiedBy')->getValue(),
            'Modified by should be automatically set to user name'
        );
    }

}