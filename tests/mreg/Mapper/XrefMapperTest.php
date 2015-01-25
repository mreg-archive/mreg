<?php
namespace mreg\Mapper;

use mreg\Model\Xref\Xref;
use mreg\Model\Dir\Faction;
use itbz\datamapper\pdo\Search;
use itbz\datamapper\pdo\Expression;
use mreg\NullObject\NullDate;

class XrefMapperTest extends \PHPUnit_Framework_TestCase
{

    function testFindXrefForeigns()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findByPk'),
            array(),
            '',
            FALSE
        );

        $foreign = new Faction;
        $foreign->setId('x');

        $foreignMapper->expects($this->atLeastOnce())
                      ->method('findByPk')
                      ->with('x')
                      ->will($this->returnValue($foreign));

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('findMany'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );

        $xref = new Xref;
        $xref->setForeignId('x');
        $xref->setState('OK');

        $xrefMapper->expects($this->once())
                   ->method('findMany')
                   ->with(array(
                        'master_id' => 1,
                        new Expression('state', 'OK')
                   ))
                   ->will($this->returnValue(array($xref)));
        
        $master = new Faction;
        $master->setId(1);
        $result = $xrefMapper->findXrefForeigns(
            $master,
            new Search,
            new Expression('state', 'OK')
        );
        

        list($returnXref, $returnEntity) = array_pop($result);
        
        $this->assertEquals('x', $returnEntity->getId());
        $this->assertEquals('x', $returnXref->getForeignId());
    }


    function testFindXrefMasters()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findByPk'),
            array(),
            '',
            FALSE
        );

        $foreign = new Faction;
        $foreign->setId('x');

        $foreignMapper->expects($this->atLeastOnce())
                      ->method('findByPk')
                      ->with('x')
                      ->will($this->returnValue($foreign));

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('findMany'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );

        $xref = new Xref;
        $xref->setMasterId('x');
        $xref->setState('OK');
        
        $xrefMapper->expects($this->once())
                   ->method('findMany')
                   ->with(array(
                        'foreign_id' => 1,
                        new Expression('state', 'OK')
                   ))
                   ->will($this->returnValue(array($xref)));
        
        $master = new Faction;
        $master->setId(1);
        $result = $xrefMapper->findXrefMasters(
            $master,
            new Search,
            new Expression('state', 'OK')
        );
        
        list($returnXref, $returnEntity) = array_pop($result);
        
        $this->assertEquals('x', $returnEntity->getId());
        $this->assertEquals('x', $returnXref->getMasterId());
    }


    public function testLink()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('save'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );
        
        $expectedXref = new Xref;
        $expectedXref->setMasterId(1);
        $expectedXref->setForeignId(2);
        $expectedXref->setSince(new \DateTime('@1'));
        $expectedXref->setState('OK');
        // Clone expected to create null unto data
        $expectedXref = clone $expectedXref;
        
        $xrefMapper->expects($this->once())
                   ->method('save')
                   ->with($expectedXref)
                   ->will($this->returnValue(1));
        
        $nRows = $xrefMapper->link(1, 2, new \DateTime('@1'));
        $this->assertSame(1, $nRows);
    }


    public function testUnlink()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('find', 'delete'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );

        $xrefMapper->expects($this->once())
                   ->method('find')
                   ->with(array(
                       'master_id' => 1,
                       'foreign_id' => 2,
                       'state' => 'OK'
                   ))
                   ->will($this->returnValue(new Xref));

        $xrefMapper->expects($this->once())
                   ->method('delete')
                   ->will($this->returnValue(1));

        $nRows = $xrefMapper->unlink(1, 2);
        $this->assertSame(1, $nRows);
    }


    public function testDeactivate()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('find', 'save'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );

        $xrefMapper->expects($this->once())
                   ->method('find')
                   ->with(array(
                       'master_id' => 1,
                       'foreign_id' => 2,
                       'state' => 'OK'
                   ))
                   ->will($this->returnValue(new Xref));

        $xrefMapper->expects($this->once())
                   ->method('save')
                   ->will($this->returnValue(1));

        $nRows = $xrefMapper->deactivate(
            1,
            2,
            'state',
            'comment',
            new \DateTime('@1')
        );
        $this->assertSame(1, $nRows);
    }


    public function testUnlinkAll()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $xrefMapper = $this->getMock(
            '\mreg\Mapper\XrefMapper',
            array('findMany', 'delete'),
            array($table, new Xref, $foreignMapper, $foreignMapper)
        );

        $xrefMapper->expects($this->atLeastOnce())
                   ->method('findMany')
                   ->will($this->returnValue(array(new Xref)));

        $xrefMapper->expects($this->atLeastOnce())
                   ->method('delete')
                   ->will($this->returnValue(1));

        $nRows = $xrefMapper->unlinkAll(1);
        $this->assertSame(2, $nRows);
    }


    function testSetGetAuthUser()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );

        $foreignMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('setAuthUser', 'getAuthUser'),
            array(),
            '',
            FALSE
        );

        $user = new \mreg\NullObject\AnonymousUser;
        $user->setCreated(new NullDate);
        $user->setModified(new NullDate);

        $foreignMapper->expects($this->exactly(2))
                      ->method('setAuthUser')
                      ->with($user);

        $foreignMapper->expects($this->once())
                      ->method('getAuthUser')
                      ->will($this->returnValue($user));

        $mapper = new XrefMapper(
            $table,
            new Xref,
            $foreignMapper,
            $foreignMapper
        );

        $mapper->setAuthUser($user);
        $this->assertSame($user, $mapper->getAuthUser());
    }

}
