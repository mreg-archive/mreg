<?php
namespace mreg\tests\Economy;

use mreg\Economy\Accountant;
use mreg\Economy\Channels;
use mreg\Economy\TableOfDebits;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\ChartOfTemplates;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\Template;
use itbz\stb\Utils\Amount;
use itbz\stb\Accounting\Formatter\SIE;

class AccountantTest extends AbstractEconomyTestCase
{

    function testSetGetAccounts()
    {
        $a = new Accountant(new SIE());
        $accounts = new ChartOfAccounts();
        $a->setAccounts($accounts);
        $this->assertEquals($accounts, $a->getAccounts());
    }


    function testSetGetChannels()
    {
        $a = new Accountant(new SIE());
        $channels = new Channels();
        $a->setChannels($channels);
        $this->assertEquals($channels, $a->getChannels());
    }


    function testSetGetTemplates()
    {
        $a = new Accountant(new SIE());
        $templates = new ChartOfTemplates();
        $a->setTemplates($templates);
        $this->assertEquals($templates, $a->getTemplates());
    }


    function testSetGetDebits()
    {
        $a = new Accountant(new SIE());
        $debits = new TableOfDebits();
        $a->setDebits($debits);
        $this->assertEquals($debits, $a->getDebits());
    }


    function testRrquiredTemplateMissingError()
    {
        $a = $this->getValidAccountant();

        $templates = new ChartOfTemplates();
        $a->setTemplates($templates);

        // Create empty debits so that templates from debits can not be missing
        $debits = new TableOfDebits();
        $a->setDebits($debits);

        // Required templates are missing
        $this->assertFalse($a->isValid($msg));
    }


    function testRequiredChannelsMissingError()
    {
        $a = $this->getValidAccountant();
        $channels = new Channels();
        $a->setChannels($channels);

        // Required channels are missing
        $this->assertFalse($a->isValid($msg));
    }
    
    
    function testChannelAcountMissingError()
    {
        $a = $this->getValidAccountant();

        $channels = $a->getChannels();
        $channels->addChannel('PG', new Account('9999', 'T', 'PlusGiro'));
        $a->setChannels($channels);

        // Account 9999 is missing from account plan
        $this->assertFalse($a->isValid($msg));
    }
    
    
    function testChannelAccountDifferentError()
    {
        $a = $this->getValidAccountant();

        $accounts = $a->getAccounts();
        $accounts->addAccount(new Account('1920', 'T', 'FOOBAR'));
        $a->setAccounts($accounts);

        // Account 1920 is different in account plan
        $this->assertFalse($a->isValid($msg));
    }


    function testTemplateAccountMissingError()
    {
        $a = $this->getValidAccountant();

        $templates = $a->getTemplates();
        $template = new Template();
        $template->addTransaction('9999', '{SUB}');
        $templates->addTemplate($template);
        $a->setTemplates($templates);

        // Account 1920 is not in account plan
        $this->assertFalse($a->isValid($msg));

        $accounts = $a->getAccounts();
        $accounts->addAccount(new Account('9999', 'T', 'Bank'));
        $a->setAccounts($accounts);

        // Now account matches
        $this->assertTrue($a->isValid($msg));
    }


    function testDebitsTemplateMissing()
    {
        $a = $this->getValidAccountant();

        $debits = new TableOfDebits();
        $debits->setClass('A', new Amount(0), 'templ', new Amount(0),array());
        $a->setDebits($debits);

        // Template 'tmpl' is missing
        $this->assertFalse($a->isValid($msg));

        $templates = $a->getTemplates();
        $template = new Template('templ');
        $templates->addTemplate($template);
        $a->setTemplates($templates);

        // Now template exists
        $this->assertTrue($a->isValid($msg));
    }


    function testExportImportTemplates()
    {
        $a = new Accountant(new SIE());
        $templates = new ChartOfTemplates();
        $template = new Template('templ');
        $template->addTransaction('1920', '{SUB}');
        $templates->addTemplate($template);
        $a->setTemplates($templates);

        $str = $a->exportTemplates();

        $a2 = new Accountant(new SIE());
        $a2->importTemplates($str);
        $templates2 = $a2->getTemplates();
        $this->assertTrue($templates2->exists('templ'));
    }


    function testExportImportAccounts()
    {
        $a = new Accountant(new SIE());
        $accounts = new ChartOfAccounts();
        $accounts->addAccount(new Account('1920', 'T', 'Bank'));
        $a->setAccounts($accounts);

        $str = $a->exportAccounts('accounts');
        
        $a2 = new Accountant(new SIE());
        $a2->importAccounts($str);
        $accounts2 = $a2->getAccounts();
        $this->assertTrue($accounts2->accountExists('1920'));
    }

    function testExtract()
    {
        $a = new Accountant(new SIE());
        $this->assertEquals(array(), $a->extract(1, array()));
    }

}
