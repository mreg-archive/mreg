<?php
namespace mreg\Model\Xref;


class XrefTest extends \PHPUnit_Framework_TestCase
{

    function testImportExport()
    {
        $xref = new Xref();
        $data = array(
            'id' => '1',
            'tSince' => '1',
            'tUnto' => '1',
            'master_id' => '2',
            'foreign_id' => '3',
            'state' => 'OK',
            'stateComment' => 'state ok'
        );
        $xref->load($data);

        $this->assertEquals($data, $xref->extract(1, array()));
    }


    function testGetSince()
    {
        $xref = new Xref();
        $this->assertEquals(
            'Datum saknas',
            $xref->getSince()->format(\DATETIME::ATOM)
        );

        $xref->setSinceTimestamp(1);
        $this->assertEquals(
            '1970-01-01T01:00:01+01:00',
            $xref->getSince()->format(\DATETIME::ATOM)
        );
    }


    function testGetUnto()
    {
        $xref = new Xref();
        $this->assertEquals(
            'Datum saknas',
            $xref->getUnto()->format(\DATETIME::ATOM)
        );

        $xref->setUntoTimestamp(1);
        $this->assertEquals(
            '1970-01-01T01:00:01+01:00',
            $xref->getUnto()->format(\DATETIME::ATOM)
        );
    }


    function testCondSetState()
    {
        $xref = new Xref();
        $xref->setState('OK');
        $this->assertFalse($xref->condSetState('ERROR', 'OK'));
        $this->assertTrue($xref->condSetState('OK', 'ERROR'));
        $this->assertEquals('ERROR', $xref->getState());
    }


    function testDeactivate()
    {
        $xref = new Xref();
        $this->assertFalse($xref->deactivate('INACTIVE'));
        $xref->setState('OK');
        $this->assertTrue($xref->deactivate(
            'INACTIVE',
            'comment',
            new \DateTime('@1')
        ));
        $this->assertEquals('INACTIVE', $xref->getState());
        $this->assertEquals('comment', $xref->getStateComment());
        $this->assertEquals(
            '1970-01-01T01:00:01+01:00',
            $xref->getUnto()->format(\DATETIME::ATOM)
        );
    }


    function testClone()
    {
        $xref = new Xref();
        $xref->setSince(new \DateTime('@1'));
        $xref->setUnto(new \DateTime('@1'));

        $clone = clone $xref;
        $this->assertFalse($xref->getSince() === $clone->getSince());
        $this->assertFalse($xref->getUnto() === $clone->getUnto());
    }


    function testExport()
    {
        $xref = new Xref();
        $xref->setSince(new \DateTime('@1'));
        $xref->setUnto(new \DateTime('@1'));
        $xref->setState('OK');
        $xref->setStateComment('comment');
        
        $expected = array(
            'tSince' => '1970-01-01T01:00:01+01:00',
            'tUnto' => '1970-01-01T01:00:01+01:00',
            'state' => 'OK',
            'stateComment' => 'comment',
        );
        $this->assertEquals($expected, $xref->export());
    }

}
