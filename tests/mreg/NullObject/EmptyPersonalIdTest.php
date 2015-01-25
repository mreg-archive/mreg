<?php
namespace mreg\NullObject;


class EmptyPersonalIdTest extends \PHPUnit_Framework_TestCase
{

    public function testAll()
    {
        $id = new EmptyPersonalId();
        $id->setId('');
        $this->assertSame('O', $id->getSex());
    }

}
