<?php
namespace mreg\Model\Dir;

use itbz\httpio\Request;

class MemberTest extends \PHPUnit_Framework_TestCase
{

    function testGetAddressee()
    {
        $member = new Member();

        $expected = array(
            'given_name' => '',
            'surname' => ''
        );
        
        $this->assertEquals($expected, $member->getAddressee());

        $member->setNames('Foo');
        $member->setSurname('Bar');

        $expected = array(
            'given_name' => 'Foo',
            'surname' => 'Bar'
        );

        $this->assertEquals($expected, $member->getAddressee());
    }


    function testNullObjects()
    {
        $member = new Member();
        $this->assertInstanceOf(
            '\mreg\NullObject\EmptyPersonalId',
            $member->getPersonalId()
        );
        $this->assertInstanceOf(
            '\mreg\NullObject\EmptyAccount',
            $member->getBankAccount()
        );
        $this->assertInstanceOf(
            '\itbz\stb\Utils\Amount',
            $member->getSalary()
        );
        $this->assertInstanceOf(
            '\DateTime',
            $member->getDateOfBirth()
        );
    }

    function testClone()
    {
        $member1 = new Member();
        $member1->setPersonalId('820323-xxxx');
        $member1->setDateOfBirth('1982-03-23');
        $member1->setSalary('0');
        $member1->setBankAccount('0000,');
        $id1 = $member1->getPersonalId();
        $member2 = clone $member1;
        $id2 = $member2->getPersonalId();
        $this->assertFalse($id1 === $id2);
    }


    function testImportExport()
    {
        $member = new Member();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'personalId' => '820323-xxxx',
            'names' => 'Foo',
            'surname' => 'Bar',
            'dob' => '1982-03-23',
            'sex' => 'F',
            'avatar' => 'http://avatar',
            'workCondition' => 'ANNAN',
            'salary' => '1000',
            'debitClass' => 'A',
            'paymentType' => 'LS',
            'bankAccount' => '0000,123456',
            'notes' => 'foobar',
            'LS' => 'ls',
            'invoiceFlag' => '1'
        );

        $member->load($data);

        $this->assertTrue(is_array($member->export()));
        $this->assertTrue($member->hasExpiredInvoices());
    }

    

    function testLoadRequest()
    {
        $member = new Member();

        $data = array(
            'id' => '2',
            'tCreated' => '1',
            'tModified' => '1',
            'owner' => 'me',
            'group' => 'mygroup',
            'mode' => '511',
            'etag' => 'qwerty',
            'modifiedBy' => 'user',
            'personalId' => '820323-xxxx',
            'names' => 'Foo',
            'surname' => 'Bar',
            'dob' => '1982-03-23',
            'sex' => 'F',
            'avatar' => 'http://avatar',
            'workCondition' => 'ANNAN',
            'salary' => '1000',
            'debitClass' => 'A',
            'paymentType' => 'LS',
            'bankAccount' => '0000,123456',
            'notes' => 'foobar',
            'LS' => 'ls'
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $member->loadRequest($request);

        $this->assertTrue(is_array($member->extract(1, array())));
    }


    function testGetSexFromPersonalId()
    {
        $member = new Member();
        $this->assertSame('O', $member->getSex());
    }

}
