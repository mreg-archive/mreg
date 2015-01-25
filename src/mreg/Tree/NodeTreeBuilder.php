<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Tree
 */

namespace mreg\Tree;

use RuntimeException;


/**
 * Build a data structure for NodeTree
 *
 * @package mreg\Tree
 */
class NodeTreeBuilder
{

    /**
     * The list of unprocessed nodes
     *
     * @var array
     */
    private $_rawNodes = array();


    /**
     * Add raw node
     *
     * @param int $id
     * @param int $parentId
     * @param mixed $data
     *
     * @return NodeTreeBuilder Instance for chaining
     */
    public function addNode($id, $parentId, $data)
    {
        $this->_rawNodes[$id] = new Node($id, $parentId, array(), $data);
        
        return $this;
    }


    /**
     * Add raw root node
     *
     * A root node is represented having itself as parent. This is a shorthand
     * method.
     *
     * @param int $id
     * @param mixed $data
     *
     * @return NodeTreeBuilder Instance for chaining
     */
    public function addRootNode($id, $data)
    {
        $this->_rawNodes[$id] = new Node($id, $id, array(), $data);
        
        return $this;
    }


    /**
     * Clear registered nodes
     *
     * @return NodeTreeBuilder Instance for chaining
     */
    public function clear()
    {
        $this->_rawNodes = array();
        
        return $this;
    }


    /**
     * Get tree
     *
     * @return array Array of node objects
     *
     * @throws RuntimeException if the number of roots != 1
     * @throws RuntimeException if any parent node is not set
     */
    public function getTree()
    {
        $nodes = $this->_rawNodes;
        $rootFound = FALSE;
        foreach ($nodes as $id => $node) {
            // Find root node
            if ($node->isRoot()) {
                if ($rootFound) {
                    $msg = "Two root nodes found in tree. See id '$id'.";
                    throw new RuntimeException($msg);                
                }
                $rootFound = TRUE;
            } else {
                // Validate parentId
                if (!isset($nodes[$node->getParentId()])) {
                    $msg = "Parent of node '$id' is missing";
                    throw new RuntimeException($msg);
                }
                // Add this node as a child to parentId
                $nodes[$node->getParentId()]->addChild($id);
            }
        }
        // One root must exist
        if (!$rootFound) {
            $msg = "No root node found";
            throw new RuntimeException($msg);
        }
        
        return $nodes;
    }


    /**
     * Get tree as raw array data
     *
     * Suitable for storing tree without the need for perserving PHP objects
     *
     * @return array
     *
     * @throws RuntimeException if the number of roots != 1
     * @throws RuntimeException if any parent node is not set
     */
    public function getExportedTree()
    {
        $exported = array();
        foreach ($this->getTree() as $id => $node) {
            $exported[$id] = array(
                $node->getParentId(),
                $node->getChildrenIds(),
                $node->getData()
            );
        }
        
        return $exported;
    }

}