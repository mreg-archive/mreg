<?php
namespace mreg\Tree;


class NodeTreeBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \RuntimeException
     */
    function testNoRootError()
    {
        $builder = new NodeTreeBuilder();
        $tree = $builder->addNode(1, 2, 'data1')
                        ->addNode(2, 1, 'data2')
                        ->getTree();
    }


    /**
     * @expectedException \RuntimeException
     */
    function testMultipleRootsError()
    {
        $builder = new NodeTreeBuilder();
        $tree = $builder->addRootNode(1, 'data1')
                        ->addRootNode(2, 'data2')
                        ->getTree();
    }


    /**
     * @expectedException \RuntimeException
     */
    function testParentMissingError()
    {
        $builder = new NodeTreeBuilder();
        $tree = $builder->addRootNode(1, 'data1')
                        ->addNode(3, 2, 'data3')
                        ->getTree();
    }


    function testBuild()
    {
        $builder = new NodeTreeBuilder();
        $tree = $builder->addNode(1, 1, 'data1')
                        ->addNode(2, 1, 'data2')
                        ->addNode(3, 1, 'data3')
                        ->addNode(4, 2, 'data4')
                        ->addNode(5, 3, 'data5')
                        ->addNode(6, 3, 'data6')
                        ->getExportedTree();
        $expected = array(
            1 => array(1, array(2, 3), 'data1'),
            2 => array(1, array(4), 'data2'),
            3 => array(1, array(5, 6), 'data3'),
            4 => array(2, array(), 'data4'),
            5 => array(3, array(), 'data5'),
            6 => array(3, array(), 'data6'),
        );
        $this->assertEquals($expected, $tree);
    }


    function testClear()
    {
        $builder = new NodeTreeBuilder();
        $tree = $builder->addRootNode(1, 'data1')
                        ->addNode(2, 1, 'data2')
                        ->clear()
                        ->addRootNode(3, 'data3')
                        ->getExportedTree();
        $expected = array(
            3 => array(3, array(), 'data3'),
        );
        $this->assertEquals($expected, $tree);
    }

}
