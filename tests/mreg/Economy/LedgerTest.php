<?php
namespace mreg\tests\Economy;

use mreg\Economy\Ledger;
use itbz\stb\Utils\Amount;
use itbz\stb\Accounting\Formatter\SIE;

class LedgerTest extends AbstractEconomyTestCase
{

    function testVoid(){}

    /*
    function getMemberMock()
    {
        $member = $this->getMock(
            '\Mreg\Member',
            array('getId'),
            array(),
            '',
            FALSE
        );

        $member->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(999));

        return $member;
    }
    

    function getLedger()
    {
        $ledger = new Ledger($this->getValidAccountant(), new SIE());

        return $ledger;
    }


    /**
     * @ expectedException mreg\Exception\EconomyException
     * /
    function testInvalidAccountantError()
    {
        new Ledger($this->getInvalidAccountant(), new SIE());
    }


    function testGetDebitClassFromSalary()
    {
        $ledger = $this->getLedger();
        $member = $this->getMemberMock();
        $member->setSalary(new Amount(2000));
        $class = $ledger->getDebitClass($member);

        $this->assertEquals('A', $class->getName());
    }


    function testGetDebitClassFromPreviousValue()
    {
        $ledger = $this->getLedger();
        $member = $this->getMemberMock();
        $member->debitClass = 'B';
        $class = $ledger->getDebitClass($member);

        $this->assertEquals('B', $class->getName());
    }


    /**
     * @ expectedException mreg\Exception\EconomyException
     * /
    function testGetDebitClassFromPreviousValueError()
    {
        $ledger = $this->getLedger();
        $member = $this->getMemberMock();
        $member->debitClass = 'D';

        // Debit class D does not exist
        $class = $ledger->getDebitClass($member);
    }


    function testGetDefaultDebitClass()
    {
        $ledger = $this->getLedger();
        $member = $this->getMemberMock();
        $class = $ledger->getDebitClass($member);

        $this->assertEquals('C', $class->getName());
    }
    */
}
