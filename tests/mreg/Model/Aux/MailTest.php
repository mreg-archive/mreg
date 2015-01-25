<?php
namespace mreg\Model\Aux;
use itbz\httpio\Request;
use EmailAddressValidator;


class MailTest extends \PHPUnit_Framework_TestCase
{

    function testImportExport()
    {
        $mail = new Mail(new EmailAddressValidator());

        $mail->load(array('mail' => 'test@test.com'));
        
        $export = $mail->export('');

        $this->assertTrue($export['valid']);
        $this->assertEquals('test@test.com', $export['mail']);
    }


    function testLoadRequest()
    {
        $data = array(
            'name' => 'testname',
            'mail' => 'invalidaddress',
        );
        $request = new Request('', '', 'GET', array(), array(), array(), $data);

        $mail = new Mail(new EmailAddressValidator());
        $mail->loadRequest($request);

        $export = $mail->export('');

        $this->assertFalse($export['valid']);
        $this->assertEquals('invalidaddress', $export['mail']);
    }


    function testExtract()
    {
        $mail = new Mail(new EmailAddressValidator());

        $mail->load(array('mail' => 'test@test.com'));
        
        $export = $mail->extract(1, array());

        $this->assertTrue($export['valid']);
        $this->assertEquals('test@test.com', $export['mail']);
    }

}
