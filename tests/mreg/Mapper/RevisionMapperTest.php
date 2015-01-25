<?php
namespace mreg\Mapper;

use mreg\Model\Aux\Revision;
use itbz\datamapper\pdo\Search;

class RevisionMapperTest extends \PHPUnit_Framework_TestCase
{

    function testFindRevisions()
    {
        $table = $this->getMock(
            '\itbz\datamapper\pdo\table\Table',
            array(),
            array(),
            '',
            FALSE
        );
        
        $prototype = new Revision();
        
        $mapper = $this->getMock(
            '\mreg\Mapper\RevisionMapper',
            array('findMany'),
            array($table, $prototype)
        );
        
        $mapper->setRefTable('foobar');
        
        $mapper->expects($this->once())
            ->method('findMany')
            ->with(
                array(
                    'ref_table' => 'table',
                    'ref_column' => 'column',
                    'ref_id' => '1'
                ),
                new Search
            )
            ->will($this->returnValue(array(new Revision)));
        
        $revs = $mapper->findRevisions('1', 'column', 'table');
        $this->assertTrue(is_array($revs[0]));
    }

}
