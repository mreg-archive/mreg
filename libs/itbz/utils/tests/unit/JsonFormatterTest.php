<?php
namespace itbz\utils;

class JsonFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testIndent()
    {
        $json = JsonFormatter::format(
            array(
                'string' => 'foobar',
                'int' => 123,
                'bool' => true,
                'array' => array(1,2),
                'object' => (object)array('property'=>'foobar')
            ),
            "\t",
            "\n"
        );

        $expected = "{\n\t\"string\":\"foobar\",\n\t\"int\":123,\n\t\"bool\":true,\n";
        $expected .= "\t\"array\":[\n\t\t1,\n\t\t2\n\t],\n\t\"object\":{\n";
        $expected .= "\t\t\"property\":\"foobar\"\n\t}\n}";

        $this->assertSame($expected, $json);
    }
}
