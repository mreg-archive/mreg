<?php
namespace mreg\Log;


class UserProcessorTest extends \PHPUnit_Framework_TestCase
{

    function testInvoke()
    {
        $processor = new UserProcessor('foobar');
        $record = $processor(array());
        $this->assertTrue(is_array($record));
        $this->assertTrue(is_array($record['extra']));
        $this->assertSame('foobar', $record['extra']['user']);
    }

}
