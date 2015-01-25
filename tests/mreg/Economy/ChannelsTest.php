<?php
namespace mreg\tests\Economy;

use mreg\Economy\Channels;
use itbz\stb\Accounting\Account;

class ChannelsTest extends AbstractEconomyTestCase
{

    function testAddGetExist()
    {
        $c = new Channels();
        
        // Channel does not exist
        $this->assertFalse($c->exists('PG'));

        $pg = new Account('1920', 'I', 'PlusGiro');
        $c->addChannel('PG', $pg);

        // Now it does
        $this->assertTrue($c->exists('PG'));

        // Test getting channel
        $this->assertEquals($pg, $c->getAccount('PG'));

        // Check complete structure
        $channels = $c->getChannels();
        $this->assertEquals($pg, $channels['PG']);
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testInvalidChannel()
    {
        $c = new Channels();
        $c->getAccount('PG');        
    }


    function testExportImport()
    {
        $c = new Channels();
        $pg = new Account('1920', 'I', 'PlusGiro');
        $bg = new Account('1930', 'I', 'Bankgiro');
        $c->addChannel('PG', $pg);
        $c->addChannel('BG', $bg);
        
        $str = serialize($c);
        $c2 = unserialize($str);
        
        $this->assertTrue($c2->exists('PG'));
        $this->assertEquals($pg, $c2->getAccount('PG'));

        $this->assertTrue($c2->exists('BG'));
        $this->assertEquals($bg, $c2->getAccount('BG'));
    }

}
