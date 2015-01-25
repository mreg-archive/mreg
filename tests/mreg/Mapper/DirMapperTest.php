<?php
namespace mreg\Mapper;

use itbz\datamapper\pdo\ExpressionSet;
use itbz\datamapper\pdo\Expression;
use mreg\Model\Aux\Address;
use mreg\Model\Aux\Mail;
use mreg\Model\Aux\Phone;
use mreg\Model\Aux\Revision;
use itbz\datamapper\pdo\Search;
use mreg\Model\Dir\Faction;
use EmailAddressValidator;

class DirMapperTest extends \PHPUnit_Framework_TestCase
{

    function getUserMock()
    {
        $user = $this->getMock(
            '\mreg\Model\Sys\User',
            array('getName', 'getGroups'),
            array(),
            '',
            FALSE
        );

        $user->expects($this->atLeastOnce())
             ->method('getName')
             ->will($this->returnValue('root'));

        $user->expects($this->atLeastOnce())
             ->method('getGroups')
             ->will($this->returnValue(array()));

        return $user;
    }


    function testFindRevisions()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array(),
            array(),
            '',
            FALSE
        );
        
        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('findRevisions'),
            array(),
            '',
            FALSE
        );

        $revisionMapper->expects($this->once())
            ->method('findRevisions')
            ->with('1', '2', '3', NULL)
            ->will($this->returnValue(array()));

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );
        
        $arr = $mapper->findRevisions('1', '2', '3');
        $this->assertTrue(is_array($arr));
    }


    /**
     * @expectedException mreg\Exception
     */
    function testMapperIdError()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array(),
            array(),
            '',
            FALSE
        );

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());
        
        // Invalid mapper id
        $mapper->findAssociated(array(), 9999);
    }


    function testGetNewAssociated()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array(),
            array(),
            '',
            FALSE
        );

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('getNewModel'),
            array(),
            '',
            FALSE
        );

        $revisionMapper->expects($this->once())
                       ->method('getNewModel')
                       ->will($this->returnValue(new Revision()));

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());

        $rev = $mapper->getNewAssociated(DirMapper::ASSOC_REVISION);
        $this->assertInstanceOf('\mreg\Model\Aux\Revision', $rev);
    }


    function testFindManyAssociated()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array('getName'),
            array(),
            '',
            FALSE
        );

        $table->expects($this->atLeastOnce())
              ->method('getName')
              ->will($this->returnValue('testtable'));

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('findMany'),
            array(),
            '',
            FALSE
        );

        $revision = new Revision();
        $revision->load(array(
            'ref_column' => 'testcol',
        ));
        
        $revisionMapper->expects($this->once())
                    ->method('findMany')
                    ->with(
                          array(
                              'ref_table' => 'testtable',
                              'ref_id' => '1'
                          )
                    )
                    ->will($this->returnValue(array($revision)));

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());
        
        $revs = $mapper->findManyAssociated(
            array(
                'ref_id' => '1'
            ),
            new Search(),
            DirMapper::ASSOC_REVISION
        );
        
        $this->assertEquals('testcol', $revs[0]->getRefColumn());
    }


    function testFindAssociated()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array('getName'),
            array(),
            '',
            FALSE
        );

        $table->expects($this->atLeastOnce())
              ->method('getName')
              ->will($this->returnValue('testtable'));

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('findMany'),
            array(),
            '',
            FALSE
        );

        $revision = new Revision();
        
        $revision->load(array(
            'ref_column' => 'testcol',
        ));
        
        $revisionMapper->expects($this->once())
                    ->method('findMany')
                    ->with(
                          array(
                              'ref_table' => 'testtable',
                              'ref_id' => '1',
                              'name' => 'testname'
                          )
                    )
                    ->will($this->returnValue(array($revision)));

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());

        $conds = array(
            'ref_id' => '1',
            'name' => 'testname'
        );

        $rev = $mapper->findAssociated($conds, DirMapper::ASSOC_REVISION);
        
        $this->assertEquals('testcol', $rev->getRefColumn());
    }


    function testSaveAssociated()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array('getName'),
            array(),
            '',
            FALSE
        );

        $table->expects($this->atLeastOnce())
              ->method('getName')
              ->will($this->returnValue('testtable'));

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('save'),
            array(),
            '',
            FALSE
        );

        $saveRev = new Revision();
        $saveRev->setCreated(new \DateTime('@1'));
        $saveRev->setModified(new \DateTime('@1'));
        $saveRev->setRefTable('testtable');
        $saveRev->setEtag('');
        $saveRev->setModifiedBy('root');

        $revisionMapper->expects($this->once())
                    ->method('save')
                    ->with($saveRev)
                    ->will($this->returnValue(1));

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());
        
        $rev = new Revision();
        $rev->setCreated(new \DateTime('@1'));
        $rev->setModified(new \DateTime('@1'));
        
        $nRows = $mapper->saveAssociated(
            $rev,
            DirMapper::ASSOC_REVISION
        );

        $this->assertSame(1, $nRows);
    }


    function testDeleteAssociated()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\access\AcTable',
            array(),
            array(),
            '',
            FALSE
        );

        $prototype = $this->getMock(
            '\itbz\datamapper\ModelInterface'
        );

        $revisionMapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('delete'),
            array(),
            '',
            FALSE
        );

        $saveRev = new Revision();
        $saveRev->setCreated(new \DateTime('@1'));
        $saveRev->setModified(new \DateTime('@1'));

        $revisionMapper->expects($this->once())
                    ->method('delete')
                    ->with($saveRev)
                    ->will($this->returnValue(1));

        $voidAuxMapper = $this->getMock(
            '\itbz\datamapper\pdo\Mapper',
            array(),
            array(),
            '',
            FALSE
        );

        $mapper = new DirMapper(
            $table,
            $prototype,
            $voidAuxMapper,
            $voidAuxMapper,
            $voidAuxMapper,
            $revisionMapper
        );

        $mapper->setAuthUser($this->getUserMock());
        
        $rev = new Revision();
        $rev->setCreated(new \DateTime('@1'));
        $rev->setModified(new \DateTime('@1'));
        
        $nRows = $mapper->deleteAssociated(
            $rev,
            DirMapper::ASSOC_REVISION
        );

        $this->assertSame(1, $nRows);
    }

}