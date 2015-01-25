<?php
namespace mreg\Tree;


class NodeTreeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \RuntimeException
     */
    function testGetNodeError()
    {
        $tree = new NodeTree(array());
        $tree->getNode(1);
    }


    function testConstructFromNodeObjects()
    {
        $tree = new NodeTree(array(
            1 => new Node(1, 1, array(2), ''),
            2 => new Node(2, 1, array(), '')
        ));

        $expected = new Node(1, 1, array(2), '');
        $node = $tree->getNode(1);
        $this->assertEquals($expected, $node);
    }


    function testGetParentNode()
    {
        $tree = new NodeTree(array(
            1 => array(1, array(2), ''),
            2 => array(1, array(), ''),
        ));

        $expected = new Node(1, 1, array(2), '');
        $parent = $tree->getParentNode(2);
        $this->assertEquals($expected, $parent);
    }


    /**
     * @expectedException \RuntimeException
     */
    function testGetParentIdError()
    {
        $tree = new NodeTree(array(
            1 => array(1, array(), '')
        ));
        $tree->getParentNode(1);
    }


    function testGetPath()
    {
        $tree = new NodeTree(array(
            1 => array(1, array(2, 3), 'data1'),
            2 => array(1, array(4), 'data2'),
            3 => array(1, array(), 'data3'),
            4 => array(2, array(), 'data4'),
        ));
        $path = array(
            2 => 'data2',
            1 => 'data1'
        );
        $this->assertEquals($path, $tree->getPath(4));
    }


    function testGetDescendants()
    {
        $tree = new NodeTree(array(
            1 => array(1, array(2, 3), 'data1'),
            2 => array(1, array(4), 'data2'),
            3 => array(1, array(5, 6), 'data3'),
            4 => array(2, array(), 'data4'),
            5 => array(3, array(), 'data5'),
            6 => array(3, array(), 'data6'),
        ));
        $nodes = array(
            2 => 'data2',
            4 => 'data4',
            3 => 'data3',
            5 => 'data5',
            6 => 'data6'
        );
        $this->assertEquals($nodes, $tree->getDescendants(1));
    }

}
