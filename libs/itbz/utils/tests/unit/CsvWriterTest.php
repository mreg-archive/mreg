<?php
namespace itbz\utils;

class CsvWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testAlignCsv()
    {
        $writer = new CsvWriter(",", "'", "\n", true);
        $writer->addData(
            array(
                array('foo', 'bar'),
                array('foobar')
            )
        );

        $expected = "'foo'   ,'bar'\n'foobar',     \n";

        $this->assertSame($expected, $writer->getCsv());
    }
}
