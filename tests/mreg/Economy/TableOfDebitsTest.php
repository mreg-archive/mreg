<?php
namespace mreg\tests\Economy;

use mreg\Economy\TableOfDebits;
use itbz\stb\Utils\Amount;

class TableOfDebitsTest extends AbstractEconomyTestCase
{

    function testSetGetparent()
    {
        $t = new TableOfDebits();
        $parent = new TableOfDebits();
        $t->setParent($parent);
        $this->assertEquals($parent, $t->getParent());
    }


    function testAddExistsRemoveDebitName()
    {
        $t = new TableOfDebits();
        $this->assertFalse($t->debitExists('DEBIT'));
        $t->addDebitName('DEBIT');
        $this->assertTrue($t->debitExists('DEBIT'));
        $this->assertEquals(array('DEBIT'), $t->getDebitNames());
        $t->removeDebit('DEBIT');
        $this->assertFalse($t->debitExists('DEBIT'));
        $this->assertEquals(array(), $t->getDebitNames());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testAddDebitNameError()
    {
        $t = new TableOfDebits();
        // Debit name must på uppercase
        $t->addDebitName('invalid');
    }


    function testSetClass()
    {
        $t = new TableOfDebits();
        $this->assertFalse($t->classExists('D'));

        $debits = array(
            'SYNDIKAT' => new Amount(10),
            'SF' => new Amount(5)
        );

        $t->setClass('D', new Amount(69), 'template', new Amount(0), $debits);
        $t->setClass('C', new Amount(39), 'template', new Amount(0), $debits);
        $t->setClass('B', new Amount(99), 'template', new Amount(6000), $debits);
        $t->setClass('A', new Amount(199), 'template', new Amount(13000), $debits);
        $t->setClass('AA2', new Amount(399), 'template', new Amount(19000), $debits);
        $t->setClass('AA1', new Amount(299), 'template', new Amount(24000), $debits);

        $this->assertTrue($t->classExists('D'));
        $t->removeClass('D');
        $this->assertFalse($t->classExists('D'));
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     * @dataProvider testSetClassErrorProvider
     */
    function testSetClassError($name, $charge, $template, $intervalStart, $debits)
    {
        $t = new TableOfDebits();
        $t->setClass($name, $charge, $template, $intervalStart, $debits);
    }


    function testSetClassErrorProvider()
    {
        return array(
            // Name must match "/^[A-Z]{1,2}\d{0,2}$/"
            array(123, new Amount(1), 'temp', new Amount(0), array()),
            array('1A3', new Amount(1), 'temp', new Amount(0), array()),

            // At least one class must start att interval 0
            array('A3', new Amount(1), 'temp', new Amount(100), array()),

            // Debit keys must pass ctype_alpha()
            array('A3', new Amount(1), 'temp', new Amount(0), array('lowercase'=>new Amount)),

            // Debits must be Amount instances
            array('A3', new Amount(1), 'temp', new Amount(0), array('UPPERCASE'=>100)),
        );
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testSetClassIntervalOverlapError()
    {
        $t = new TableOfDebits();
        $t->setClass('D', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('C', new Amount(1), 'template', new Amount(100), array());
        // The next class 'B' start its interval at the same amount as 'C'
        $t->setClass('B', new Amount(1), 'template', new Amount(100), array());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testSetClassIntervalGrowthError()
    {
        $t = new TableOfDebits();
        $t->setClass('A', new Amount(1), 'template', new Amount(0), array());
        // 'B' sorts after 'A', and so 'B' interval start must be lower than 'A'
        $t->setClass('B', new Amount(1), 'template', new Amount(100), array());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testRemoveClassIntervalError()
    {
        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('A', new Amount(1), 'template', new Amount(100), array());
        // When removing class A there will be no class starting its interval at 0
        $t->removeClass('B');
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testClassDoesNotExistError()
    {
        $t = new TableOfDebits();
        $t->getClass('A');
    }


    function testRemoveDebit()
    {
        $t = new TableOfDebits();
        
        $t->setClass('A', new Amount(1), 'template', new Amount(0), array(
            'SYNDIKAT' => new Amount(10),
            'SF' => new Amount(5)
        ));
        
        $t->removeDebit('SYNDIKAT');
        
        $expected = array(
            'SF' => new Amount(5),
            'AVGIFT' => new Amount(1)
        );
        $class = $t->getClass('A');
        $this->assertEquals($expected, $class->getDebits());
    }


    function testDebitsOnlyInOneClass()
    {
        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array('SF'=>new Amount(5)));
        $t->setClass('A1', new Amount(10), 'template', new Amount(100), array());

        $expected = array(
            'AVGIFT' => new Amount(10),
            'SF' => new Amount(0)
        );
        $class = $t->getClass('A1');
        $this->assertEquals($expected, $class->getDebits());
    }


    function testClassInterval()
    {
        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('A', new Amount(1), 'template', new Amount(1000), array());
        
        $intervalB = array(new Amount(0), new Amount(999.99));
        $B = $t->getClass('B');
        $this->assertEquals($intervalB, $B->getInterval());

        $intervalA = array(new Amount(1000), new Amount(TableOfDebits::INFINITY));
        $A = $t->getClass('A');
        $this->assertEquals($intervalA, $A->getInterval());
    }


    function testGetClassWithParents()
    {
        // NOTE: parents must be createt top-down.. !
        $top = new TableOfDebits();
        $top->setClass('A', new Amount(20), 'template', new Amount(0), array('C'=>new Amount(15)));

        $middle = new TableOfDebits();
        $middle->setClass('A', new Amount(20), 'template', new Amount(0), array('B'=>new Amount(5)));
        $middle->setParent($top);

        $bottom = new TableOfDebits();
        $bottom->setClass('A1', new Amount(10), 'template', new Amount(0), array('A'=>new Amount(10)));
        $bottom->setParent($middle);

        // Test that debits from parents are taken into account
        $debits = array(
            'AVGIFT' => new Amount(10),
            'A' => new Amount(10),
            'B' => new Amount(5),
            'C' => new Amount(15)
        );
        $class = $bottom->getClass('A1');
        $this->assertEquals($debits, $class->getDebits());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testSetParentWithClassMissingException()
    {
        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('A1', new Amount(1), 'template', new Amount(100), array());

        $parent = new TableOfDebits();
        $parent->setClass('B', new Amount(1), 'template', new Amount(0), array());

        // Class A is missing in parent
        $t->setParent($parent);
    }


    function testGetClassFromSalary()
    {
        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('A1', new Amount(10), 'template', new Amount(100), array());
        
        $class = $t->getClassFromSalary(new Amount(99));
        $this->assertEquals('B', $class->getName());

        $class = $t->getClassFromSalary(new Amount(100));
        $this->assertEquals('A1', $class->getName());
    }


    /**
     * @expectedException mreg\Exception\EconomyException
     */
    function testGetClassFromSalaryError()
    {
        $t = new TableOfDebits();
        $class = $t->getClassFromSalary(new Amount(99));
    }


    function testGetClassFromAmount()
    {
        $t = new TableOfDebits();
        $t->setClass('C', new Amount(10), 'template', new Amount(0), array());
        $t->setClass('B', new Amount(100), 'template', new Amount(50), array());
        $t->setClass('A1', new Amount(200), 'template', new Amount(100), array());
    
        $class = $t->getClassFromAmount(new Amount(200));
        $this->assertEquals('A1', $class->getName());

        $class = $t->getClassFromAmount(new Amount(300));
        $this->assertEquals('A1', $class->getName());

        $class = $t->getClassFromAmount(new Amount(100));
        $this->assertEquals('B', $class->getName());

        $class = $t->getClassFromAmount(new Amount(1));
        $this->assertEquals('C', $class->getName());
    }


    function testSerialize()
    {
        $parent = new TableOfDebits();
        $parent->setClass('B', new Amount(20), 'template', new Amount(0), array('SF'=>new Amount(5)));
        $parent->setClass('A', new Amount(20), 'template', new Amount(100), array('SF'=>new Amount(5)));

        $t = new TableOfDebits();
        $t->setClass('B', new Amount(1), 'template', new Amount(0), array());
        $t->setClass('A1', new Amount(10), 'template', new Amount(100), array());
        $t->setParent($parent);
        
        $str = serialize($t);
        
        $t2 = unserialize($str);

        // Parent is not loaded at this time
        $expected = array(
            'AVGIFT' => new Amount(1)
        );
        $class = $t2->getClass('B');
        $this->assertEquals($expected, $class->getDebits());

        // Reload parent
        $t2->setParent($parent);
        $expected['SF'] = new Amount(5);
        $class = $t2->getClass('B');
        $this->assertEquals($expected, $class->getDebits());
    }


    /**
     * Baserat på Malmö LS avgiftstabell
     *   Klass 	Lön i tusen Avgift 	CENTRAL Distr.  Synd.   Strid.
     *   AA 1 	24- 	    399 	190 	5 	    10 	    5
     *   AA 2   19-24 	    299 	190 	5 	    10 	    5
     *   A 	    13-19 	    199 	120 	5 	    10 	    5
     *   B 	    6 till 13 	99 	    50 	    5 	    10 	    5
     *   C 	    0 till 6 	39 	    0 	    0 	    10 	    5
     *   D 	    stud 	    69 	    0 	    0 	    10 	    5
     */
    function testFullLsTable()
    {
        $central = new TableOfDebits();
        $central->setClass('D', new Amount(0), '', new Amount(0), array());
        $central->setClass('C', new Amount(0), '', new Amount(0), array());
        $central->setClass('B', new Amount(50), '', new Amount(6000), array('CENTRAL'=>new Amount(50)));
        $central->setClass('A', new Amount(120), '', new Amount(13000), array('CENTRAL'=>new Amount(120)));
        $central->setClass('AA', new Amount(190), '', new Amount(19000), array('CENTRAL'=>new Amount(190)));

        $sodra = new TableOfDebits();
        $sodra->setClass('D', new Amount(0), '', new Amount(0), array());
        $sodra->setClass('C', new Amount(0), '', new Amount(0), array());
        $sodra->setClass('B', new Amount(5), '', new Amount(6000), array('SODRA'=>new Amount(5)));
        $sodra->setClass('A', new Amount(5), '', new Amount(13000), array('SODRA'=>new Amount(5)));
        $sodra->setClass('AA', new Amount(5), '', new Amount(19000), array('SODRA'=>new Amount(5)));
        $sodra->setParent($central);

        $malmo = new TableOfDebits();
        $malmoDebits = array(
            'SYNDIKAT' => new Amount(10),
            'SF' => new Amount(5)
        );
        $malmo->setClass('D', new Amount(69), 't', new Amount(0), $malmoDebits);
        $malmo->setClass('C', new Amount(39), 't', new Amount(0), $malmoDebits);
        $malmo->setClass('B', new Amount(99), 't', new Amount(6000), $malmoDebits);
        $malmo->setClass('A', new Amount(199), 't', new Amount(13000), $malmoDebits);
        $malmo->setClass('AA2', new Amount(299), 't', new Amount(19000), $malmoDebits);
        $malmo->setClass('AA1', new Amount(399), 't', new Amount(24000), $malmoDebits);
        $malmo->setParent($sodra);

        $classes = $malmo->getClasses();

        // Test class D
        $D = $classes['D'];
        $this->assertEquals('D', $D->getName());
        $this->assertEquals(array(new Amount(0), new Amount(0)), $D->getInterval());
        $this->assertEquals(new Amount(69), $D->getCharge());
        $debits = array(
            'AVGIFT' => new Amount(69),
            'CENTRAL' => new Amount(0),
            'SODRA' => new Amount(0),
            'SYNDIKAT' => new Amount(10),
            'SF' => new Amount(5)
        );
        $this->assertEquals($debits, $D->getDebits());

        // Test class A
        $A = $classes['A'];
        $this->assertEquals('A', $A->getName());
        $this->assertEquals(array(new Amount(13000), new Amount(18999.99)), $A->getInterval());
        $this->assertEquals(new Amount(199), $A->getCharge());
        $debits = array(
            'AVGIFT' => new Amount(199),
            'CENTRAL' => new Amount(120),
            'SODRA' => new Amount(5),
            'SYNDIKAT' => new Amount(10),
            'SF' => new Amount(5)
        );
        $this->assertEquals($debits, $A->getDebits());
    }

}
