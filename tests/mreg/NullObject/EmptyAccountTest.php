<?php
namespace mreg\NullObject;


class EmptyAccountTest extends \PHPUnit_Framework_TestCase
{

    public function testAll()
    {
        $a = new EmptyAccount();
        //$this->assertSame('', (string)$a);
        //$this->assertSame('', $a->to16());
        //$this->assertSame('', $a->getClearing());
        //$this->assertSame('', $a->getNumber());
        $this->assertTrue($a->isValidClearing(''));
        $this->assertTrue($a->isValidStructure(''));
        $this->assertTrue($a->isValidCheckDigit('', ''));
        $this->assertSame('Empty account', $a->getType());
    }

}
