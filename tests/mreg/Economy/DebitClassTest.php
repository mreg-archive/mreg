<?php
namespace mreg\tests\Economy;

use mreg\Economy\DebitClass;
use itbz\stb\Utils\Amount;

class DebitClassTest extends AbstractEconomyTestCase
{

    function testGetName()
    {
        $c = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $this->assertEquals('A', $c->getName());
    }


    function testGetCharge()
    {
        $c = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $this->assertEquals(new Amount(300), $c->getCharge());
    }


    function testGetInterval()
    {
        $c = new DebitClass('A', new Amount(300), '', new Amount(100), new Amount(200));
        $expected = array(new Amount(100), new Amount(200));
        $this->assertEquals($expected, $c->getInterval());
    }


    function testGetTemlateName()
    {
        $c = new DebitClass('A', new Amount(300), 'foo', new Amount(), new Amount());
        $this->assertEquals('foo', $c->getTemplateName());
    }


    function testAddGetDebits()
    {
        $c = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $c->addDebit('Foo', new Amount(100));
        $c->addDebit('Bar', new Amount(200));
        
        $expected = array(
            'Foo' => new Amount(100),
            'Bar' => new Amount(200),
            'AVGIFT' => new Amount(300)
        );
        $this->assertEquals($expected, $c->getDebits());
    }


    function testAddParent()
    {
        $parent = new DebitClass('A', new Amount(100), '', new Amount(), new Amount());
        $parent->addDebit('Foo', new Amount(100));

        $c = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $c->addDebit('Bar', new Amount(200));

        $c->setParent($parent);

        $expected = array(
            'Foo' => new Amount(100),
            'Bar' => new Amount(200),
            'AVGIFT' => new Amount(300)
        );
        $this->assertEquals($expected, $c->getDebits());
    }


    function testParentPrecedence()
    {
        $parent = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $parent->addDebit('Overload', new Amount(100));

        $c = new DebitClass('A', new Amount(300), '', new Amount(), new Amount());
        $c->addDebit('Overload', new Amount(200));

        $c->setParent($parent);

        $expected = array(
            'Overload' => new Amount(200),
            'AVGIFT' => new Amount(300)
        );
        $this->assertEquals($expected, $c->getDebits());
    }
    

    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testIntarvalMismatchError()
    {
        $parent = new DebitClass('A', new Amount(300), '', new Amount(0), new Amount(100));
        $c = new DebitClass('A', new Amount(300), '', new Amount(100), new Amount(110));
        $c->setParent($parent);
    }


    function testIsWithinInterval()
    {
        $c = new DebitClass('A', new Amount(0), '', new Amount(100), new Amount(200));

        $this->assertTrue($c->isWithinInterval(new Amount(100)));
        $this->assertTrue($c->isWithinInterval(new Amount(200)));

        $this->assertFalse($c->isWithinInterval(new Amount(99)));
        $this->assertFalse($c->isWithinInterval(new Amount(201)));
    }

}
