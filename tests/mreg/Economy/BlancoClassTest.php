<?php
namespace mreg\Economy;

use itbz\stb\Utils\Amount;

class BlancoClassTest extends \PHPUnit_Framework_TestCase
{

    public function testAll()
    {
        $class = new BlancoClass();
        $this->assertSame('', $class->getName());
        $this->assertEquals(new Amount('0'), $class->getCharge());
        $this->assertSame('', $class->getTemplateName());
        
        $interval = array(
            new Amount('0'),
            new Amount('0')
        );
        
        $this->assertEquals($interval, $class->getInterval());
    }

}
