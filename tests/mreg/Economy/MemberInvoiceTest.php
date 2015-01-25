<?php
namespace mreg\Economy;

use itbz\stb\Accounting\Template;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\Verification;
use itbz\stb\Accounting\Transaction;
use itbz\stb\utils\Amount;
use DateTime;
use mreg\NullObject\NullDate;
use itbz\utils\Serializer;

class MemberInvoiceTest extends \PHPUnit_Framework_TestCase
{

    function testLoadRequest()
    {
        $invoice = new MemberInvoice();
        $request = new \itbz\httpio\Request;
        $invoice->loadRequest($request);
    }

    function testDefaults()
    {
        $invoice = new MemberInvoice();
        $this->assertSame('', (string)$invoice->getOCR());
        $this->assertInstanceOf('\DateTime', $invoice->getCreated());
        $this->assertFalse($invoice->getCreated() instanceof NullDate);
        $this->assertInstanceOf('\DateTime', $invoice->getExpireDate());
        $this->assertFalse($invoice->isExpired());
        $this->assertFalse($invoice->isPrinted());
        $this->assertTrue($invoice->getPrintedDate() instanceof NullDate);
        $this->assertFalse($invoice->isAutogiro());
        $this->assertFalse($invoice->isLocked());
        $this->assertTrue($invoice->getAmount()->equals(new Amount(0)));
        $this->assertTrue($invoice->isBlanco());
        $this->assertFalse($invoice->isPaid());
        $this->assertTrue($invoice->getPaidDate() instanceof NullDate);
        $this->assertSame('', $invoice->getPaidVia());
        $this->assertFalse($invoice->isExported());
        $this->assertTrue($invoice->getExportedDate() instanceof NullDate);
    }


    function testLoad()
    {
        $invoice = new MemberInvoice();
        $invoice->load(array(
            'title' => '',
            'id' => '2',
            'tCreated' => '1',
            'recipientId' => '2',
            'payerId' => '3',
            'ocr' => '133',
            'tExpire' => '1',
            'tPrinted' => '0',
            'tPaid' => '0',
            'tExported' => '0',
            'amount' => '10.10',
            'isAutogiro' => '1',
            'paidVia' => '',
            'locked' => '0',
            'description' => 'foobar',
            'template' => '',
            'verification' => '',
        ));
 
        $this->assertSame('2', $invoice->getId());
        $this->assertSame('2', $invoice->getRecipientId());
        $this->assertSame('3', $invoice->getPayerId());
        $this->assertSame('133', (string)$invoice->getOCR());
        $this->assertTrue($invoice->isExpired());
        $this->assertFalse($invoice->isPrinted());
        $this->assertFalse($invoice->isPaid());
        $this->assertFalse($invoice->isExported());
        $this->assertTrue($invoice->getAmount()->equals(new Amount(10.10)));
        $this->assertFalse($invoice->isBlanco());
        $this->assertTrue($invoice->isAutogiro());
        $this->assertFalse($invoice->isLocked());
        $this->assertSame('', $invoice->getPaidVia());
        $this->assertSame('foobar', $invoice->getDescription());
    }


    function testLoadDates()
    {
        $invoice = new MemberInvoice();
        $invoice->load(array(
            'title' => '',
            'recipientId' => '2',
            'payerId' => '3',
            'ocr' => '133',
            'tExpire' => '1',
            'tPrinted' => '1',
            'tPaid' => '1',
            'tExported' => '1',
            'amount' => '10.10',
            'isAutogiro' => '1',
            'paidVia' => '',
            'locked' => '1',
            'description' => 'foobar',
            'template' => '',
            'verification' => '',
        ));

        $this->assertTrue($invoice->isLocked());
        $this->assertTrue($invoice->isPrinted());
        $this->assertTrue($invoice->isExported());
        $this->assertFalse($invoice->getPaidDate() instanceof NullDate);
    }


    function testLoadTemplate()
    {
        $template = new Template();
        $verification = new Verification();

        $invoice = new MemberInvoice();
        $invoice->load(array(
            'title' => '',
            'recipientId' => '',
            'payerId' => '',
            'ocr' => '',
            'tExpire' => '1',
            'tPrinted' => '0',
            'tPaid' => '0',
            'tExported' => '0',
            'amount' => '',
            'isAutogiro' => '0',
            'paidVia' => '',
            'locked' => '0',
            'description' => '',
            'template' => Serializer::serialize($template),
            'verification' => Serializer::serialize($verification)
        ));

        $this->assertEquals($template, $invoice->getTemplate());
        $this->assertEquals($verification, $invoice->getVerification());
        $this->assertTrue($invoice->isPaid());
    }


    function testSetPrinted()
    {
        $invoice = new MemberInvoice();
        $invoice->setPrinted();
        $this->assertFalse($invoice->getPrintedDate() instanceof NullDate);
    }


    function testSetExported()
    {
        $invoice = new MemberInvoice();
        $invoice->setExported();
        $this->assertFalse($invoice->getExportedDate() instanceof NullDate);
    }

    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testNoVerificationError()
    {
        $i = new MemberInvoice();
        $i->getVerification();
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testUnbalancedVerificationError()
    {
        $i = new MemberInvoice();
        $ver = new Verification();
        $ver->addTransaction(
            new Transaction(new Account('1920', 'T', 'Bank'), new Amount(300))
        );
        $i->setVerification($ver);
    }


    function testExport()
    {
        $i = new MemberInvoice();
        $i->setId('1');
        $data = $i->export();
        $this->assertSame('type_invoice_member', $data['type']);
        $this->assertArrayHasKey('recipientId', $data);
        $this->assertArrayHasKey('payerId', $data);
        $this->assertArrayHasKey('ocr', $data);
        $this->assertArrayHasKey('tExpire', $data);
        $this->assertArrayHasKey('tPrinted', $data);
        $this->assertArrayHasKey('tPaid', $data);
        $this->assertArrayHasKey('tExported', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('paidVia', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('isBlanco', $data);
        $this->assertArrayHasKey('isAutogiro', $data);
        $this->assertArrayHasKey('isPaid', $data);
        $this->assertArrayHasKey('isLocked', $data);
        $this->assertArrayHasKey('isExpired', $data);
        $this->assertArrayHasKey('isPrinted', $data);
        $this->assertArrayHasKey('isExported', $data);
    }


    function testExtract()
    {
        $i = new MemberInvoice();
        $i->setId('1');
        $i->setTemplate(new Template);
        $i->setVerification(new Verification);
        $data = $i->extract(1, array());
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('tCreated', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('recipientId', $data);
        $this->assertArrayHasKey('payerId', $data);
        $this->assertArrayHasKey('ocr', $data);
        $this->assertArrayHasKey('tExpire', $data);
        $this->assertArrayHasKey('tPrinted', $data);
        $this->assertArrayHasKey('tPaid', $data);
        $this->assertArrayHasKey('tExported', $data);
        $this->assertArrayHasKey('amount', $data);
        $this->assertArrayHasKey('isAutogiro', $data);
        $this->assertArrayHasKey('paidVia', $data);
        $this->assertArrayHasKey('locked', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('template', $data);
        $this->assertArrayHasKey('verification', $data);
    }


    function testExportToAccountingError()
    {
        $i = new MemberInvoice();
        $i->setVerification(new Verification);
        $i->exportToAccounting();
        $this->assertTrue($i->isExported());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testPayNoTemplateError()
    {
        $i = new MemberInvoice();
        $i->pay(
            '',
            new Amount,
            new Account('1920', 'T', 'Bank'),
            new ChartOfAccounts
        );
    }


    function testPay()
    {
        $t = new Template();
        $t->addTransaction('{BETKANAL}', '{SUMMA}');
        $t->addTransaction('3000', '-{SUMMA}');

        $i = new MemberInvoice();
        $i->setId('1');
        $i->setAmount(new Amount(300));
        $i->setTemplate($t);

        // Invoice should not be payed
        $this->assertFalse($i->isPaid());
        
        $channel = new Account('1920', 'T', 'Bank');
        $income = new Account('3000', 'I', 'Income');
        $chart = new ChartOfAccounts();
        $chart->addAccount($channel);
        $chart->addAccount($income);
        $ver = $i->pay('PG', new Amount(300), $channel, $chart);
        
        // Invoice should be payed
        $this->assertTrue($i->isPaid());
        $this->assertSame('PG', $i->getPaidVia());
        
        $expected = new Verification();
        $expected->addTransaction(new Transaction($channel, new Amount(300)));
        $expected->addTransaction(new Transaction($income, new Amount(-300)));
        $expected->setDate($ver->getDate());
        
        // Verification should be correct
        $this->assertEquals($expected, $ver);
        
        $data = $i->export();
        $this->assertTrue(count($data['transactions']) > 0);
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testPaySubsitutionError()
    {
        $t = new Template();
        $t->addTransaction('1920', '{FOOBAR}');

        $i = new MemberInvoice();
        $i->setId('1');
        $i->setAmount(new Amount(300));
        $i->setTemplate($t);

        $channel = new Account('1920', 'T', 'Bank');
        $chart = new ChartOfAccounts();
        $chart->addAccount($channel);
        $ver = $i->pay('PG', new Amount(300), $channel, $chart);
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testPayAccountMissingError()
    {
        $t = new Template();
        $t->addTransaction('1920', '100');
        $t->addTransaction('3000', '-100');

        $i = new MemberInvoice();
        $i->setId('1');
        $i->setAmount(new Amount(300));
        $i->setTemplate($t);

        $channel = new Account('1920', 'T', 'Bank');
        $chart = new ChartOfAccounts();
        $chart->addAccount($channel);
        $ver = $i->pay('PG', new Amount(300), $channel, $chart);
    }


    function testClaimCallback()
    {
        // Create template with claim
        $t = new Template();
        $t->addTransaction('{BETKANAL}', '{SUMMA}');
        $t->addTransaction('3000', '-300');
        $t->addTransaction('1510', '{FORDRAN}');

        $i = new MemberInvoice();
        $i->setId('1');
        $i->setAmount(new Amount(300));
        $i->setTemplate($t);

        // Create accounts
        $channel = new Account('1920', 'T', 'Bank');
        $income = new Account('3000', 'I', 'Income');
        $claim = new Account('1510', 'T', 'Claim');
        $chart = new ChartOfAccounts();
        $chart->addAccount($channel);
        $chart->addAccount($income);
        $chart->addAccount($claim);

        // Called when a claim is calculated
        $phpunit = $this;
        $claimCallback = function($invoice, $claim) use ($phpunit, $i){
            $phpunit->assertSame($i, $invoice);
            $phpunit->assertEquals(100.00, $claim->getFloat());
        };

        // Pay
        $ver = $i->pay(
            'PG',
            new Amount(200),
            $channel,
            $chart,
            new DateTime(),
            $claimCallback
        );

        // Verification should contain claim
        $expected = new Verification();
        $expected->addTransaction(new Transaction($channel, new Amount(200)));
        $expected->addTransaction(new Transaction($income, new Amount(-300)));
        $expected->addTransaction(new Transaction($claim, new Amount(100)));

        $expected->setDate($ver->getDate());

        $this->assertEquals($expected, $ver);
    }

    
    function testBenefitCallback()
    {
        // Create template with Benefit
        $t = new Template();
        $t->addTransaction('{BETKANAL}', '{SUMMA}');
        $t->addTransaction('3000', '-300');
        $t->addTransaction('3990', '-{REST}');

        $i = new MemberInvoice();
        $i->setId('1');
        $i->setAmount(new Amount(300));
        $i->setTemplate($t);

        // Create accounts
        $channel = new Account('1920', 'T', 'Bank');
        $income = new Account('3000', 'I', 'Income');
        $benefit = new Account('3990', 'I', 'Benefit');
        $chart = new ChartOfAccounts();
        $chart->addAccount($channel);
        $chart->addAccount($income);
        $chart->addAccount($benefit);

        // Called when a benefit is calculated
        $phpunit = $this;
        $benefitCallback = function($invoice, $benefit) use ($phpunit, $i){
            $phpunit->assertSame($i, $invoice);
            $phpunit->assertEquals(100.00, $benefit->getFloat());
        };
        
        // Pay
        $ver = $i->pay(
            'PG',
            new Amount(400),
            $channel,
            $chart,
            new DateTime(),
            NULL,
            $benefitCallback
        );

        // Verification should contain benefit
        $expected = new Verification();
        $expected->addTransaction(new Transaction($channel, new Amount(400)));
        $expected->addTransaction(new Transaction($income, new Amount(-300)));
        $expected->addTransaction(new Transaction($benefit, new Amount(-100)));

        $expected->setDate($ver->getDate());

        $this->assertEquals($expected, $ver);
    }

}
