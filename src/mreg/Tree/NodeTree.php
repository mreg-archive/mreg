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
 * Handle a simple tree of nodes
 *
 * @package mreg\Tree
 */
class NodeTree
{

    /**
     * Array of loaded node objects
     *
     * @var array
     */
    private $_nodes = array();


    /**
     * Create object from array of nodes
     *
     * @param array $nodes Node ids as keys. Nodes as values. Nodes can be Node
     * objects or array representations. In the latter case nodes must have
     * three indices: Parent id, an array of children ids and data associated
     * with the node.
     */
    public function __construct(array $nodes)
    {
        foreach ($nodes as $id => $node) {
            if ($node instanceof Node) {
                $this->_nodes[$node->getId()] = $node;
            } else {
                $this->_nodes[$id] = new Node(
                    $id,
                    $node[0],
                    $node[1],
                    $node[2]
                );
            }
        }
    }


    /**
     * Check if id referes to a node
     *
     * @param int $id
     *
     * @return bool
     */
    public function isNode($id)
    {
        return isset($this->_nodes[$id]);
    }



    /**
     * Get node with id
     *
     * @param int $id
     *
     * @return Node
     *
     * @throws RuntimeException if node does not exist
     */
    public function getNode($id)
    {
        if (!$this->isNode($id)) {
            $msg = "Node '$id' does not exist in NodeTree";
            throw new RuntimeException($msg);
        }
        
        return $this->_nodes[$id];
    }


    /**
     * Get parent node if node with id
     *
     * @param int $id
     *
     * @return Node
     */
    public function getParentNode($id)
    {
        $node = $this->getNode($id);
        if ($node->isRoot()) {
            $msg = "Unable to fetch parent node of root node '$id'in NodeTree";
            throw new RuntimeException($msg);
        }
        
        return $this->getNode($node->getParentId());
    }


    /**
     * Get array of path to node
     *
     * @param int $id
     *
     * @return array Parent node ids as keys and associated data as values
     */
    public function getPath($id)
    {
        $path = array();
        $node = $this->getNode($id);
        while (!$node->isRoot()) {
            $node = $this->getParentNode($node->getId());
            $path[$node->getId()] = $node->getData();
        }

        return $path;
    }


    /**
     * Get descendants nodes
     *
     * @param int $id
     *
     * @return array Descendant node ids as keys and associated data as values
     */
    public function getDescendants($id)
    {
        $node = $this->getNode($id);
        if ($node->isLeaf()) {

            return array();
        }
        
        $nodes = array();
        foreach ($node->getChildrenIds() as $childId) {
            $child = $this->getNode($childId);
            $nodes[$child->getId()] = $child->getData();
            $grandchildren = $this->getDescendants($child->getId());
            foreach ($grandchildren as $gChildId => $gChildData) {
                $nodes[$gChildId] = $gChildData;
            }
        }

        return $nodes;
    }

}