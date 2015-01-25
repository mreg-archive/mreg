<?php
namespace mreg\tests\Economy;

use mreg\Economy\Channels;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\ChartOfTemplates;
use itbz\stb\Accounting\Template;
use itbz\stb\Utils\Amount;
use itbz\stb\Accounting\Formatter\SIE;
use mreg\Economy\TableOfDebits;
use mreg\Economy\Accountant;

abstract class AbstractEconomyTestCase extends \PHPUnit_Framework_TestCase
{

    function getValidAccounts()
    {
        $accounts = new ChartOfAccounts();
        $account = new Account('1910', 'T', 'Kassa');
        $accounts->addAccount($account);
        $account = new Account('1920', 'T', 'PlusGiro');
        $accounts->addAccount($account);
        $account = new Account('1930', 'T', 'Bankgiro');
        $accounts->addAccount($account);
        $account = new Account('1935', 'T', 'Autogiro');
        $accounts->addAccount($account);
        $account = new Account('3000', 'I', 'AA-avgifter');
        $accounts->addAccount($account);
        $account = new Account('3100', 'I', 'A-avgifter');
        $accounts->addAccount($account);
        $account = new Account('3200', 'I', 'B-avgifter');
        $accounts->addAccount($account);
        $account = new Account('3300', 'I', 'C-avgifter');
        $accounts->addAccount($account);
        $account = new Account('3400', 'I', 'D-avgifter');
        $accounts->addAccount($account);

       return $accounts;
    }


    function getValidChannels()
    {
        $channels = new Channels();
        $account = new Account('1910', 'T', 'Kassa');
        $channels->addChannel('K', $account);
        $account = new Account('1920', 'T', 'PlusGiro');
        $channels->addChannel('PG', $account);
        $account = new Account('1930', 'T', 'Bankgiro');
        $channels->addChannel('BG', $account);
        $account = new Account('1935', 'T', 'Autogiro');
        $channels->addChannel('AG', $account);
        $account = new Account('3000', 'I', 'AA-avgifter');
        $channels->addChannel('AA', $account);
        $account = new Account('3100', 'I', 'A-avgifter');
        $channels->addChannel('A', $account);
        $account = new Account('3200', 'I', 'B-avgifter');
        $channels->addChannel('B', $account);
        $account = new Account('3300', 'I', 'C-avgifter');
        $channels->addChannel('C', $account);
        $account = new Account('3400', 'I', 'D-avgifter');
        $channels->addChannel('D', $account);
 
        return $channels;
    }


    function getValidTemplates()
    {
        $templates = new ChartOfTemplates();
        $templates->addTemplate(new Template('MAVG'));
        $templates->addTemplate(new Template('MSKULD'));

        return $templates;
    }

    function getValidDebits()
    {
        $debits = new TableOfDebits();
        $debits->setClass('C', new Amount(0), 'MAVG', new Amount(0), array());
        $debits->setClass('B', new Amount(0), 'MAVG', new Amount(500), array());
        $debits->setClass('A', new Amount(0), 'MAVG', new Amount(1000), array());
        
        return $debits;
    }


    function getValidDebitsWithDeposition()
    {
        $deposition = array(
            'PARENT' => new Amount(10)
        );
        $debits = new TableOfDebits();
        $debits->setClass('C', new Amount(0), 'MAVG', new Amount(0), $deposition);
        $debits->setClass('B', new Amount(0), 'MAVG', new Amount(500), $deposition);
        $debits->setClass('A', new Amount(0), 'MAVG', new Amount(1000), $deposition);
        
        return $debits;
    }


    function getValidAccountant()
    {
        $accountant = new Accountant(new SIE());
        $accountant->setAccounts($this->getValidAccounts());
        $accountant->setChannels($this->getValidChannels());
        $accountant->setDebits($this->getValidDebits());
        $accountant->setTemplates($this->getValidTemplates());

        return $accountant;
    }


    function getInvalidAccountant()
    {
        $sie = new SIE();
        $accountant = new Accountant($sie);

        return $accountant;
    }

}
