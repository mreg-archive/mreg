<?php
namespace itbz\utils;

// Used when generation test material in getIncompleteClass()
// class IncompleteClass {}

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeUnserialize()
    {
        $obj = (object)array(
            'foo' => 'bar'
        );
        $str = Serializer::serialize($obj);
        $obj2 = Serializer::unserialize($str);
        $this->assertEquals($obj, $obj2);
    }

    /**
     * @expectedException itbz\utils\Exception\SerializeException
     */
    public function testUnserializeError()
    {
        Serializer::unserialize('foobar');
    }

    /**
     * @expectedException itbz\utils\Exception\SerializeException
     */
    public function testPHPincompleteClassException()
    {
        $str = $this->getIncompleteClass();
        Serializer::unserialize($str);
    }

    public function getIncompleteClass()
    {
        // Create object
        #$incomplete = new IncompleteClass();
        #$str = Serializer::serialize($incomplete);

        // Now return the serialized value with no class definition available
        $str = "TzoyOToibXJlZ1xXcmFwcGVyc1xJbmNvbXBsZXRlQ2xhc3MiOjA6e30=";

        return $str;
    }
}
