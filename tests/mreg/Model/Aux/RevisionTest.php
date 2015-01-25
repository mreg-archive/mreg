<?php
namespace mreg\Model\Aux;
use itbz\httpio\Request;


class RevisionTest extends \PHPUnit_Framework_TestCase
{

    function testImportExport()
    {
        $revision = new Revision();

        $data = array(
            'id' => '2',
            'tModified' => '1',
            'modifiedBy' => 'user',
            'ref_table' => 'testtable',
            'ref_id' => 'testid',
            'ref_column' => 'testcol',
            'old_value' => 'oldValue',
            'new_value' => 'newValue'
        );

        $revision->load($data);

        $expected = array(
            'id' => '2',
            'tModified' => '1970-01-01',
            'modifiedBy' => 'user',
            'ref_table' => 'testtable',
            'ref_column' => 'testcol',
            'ref_id' => 'testid',
            'old_value' => 'oldValue',
            'new_value' => 'newValue'
        );

        $this->assertEquals($expected, $revision->export(''));
    }


    function test100percent()
    {
        $revision = new Revision();
        $revision->loadRequest(new Request());
        $revision->extract(1, array());
        $this->assertEquals('type_revision', $revision->getType());
    }

}
