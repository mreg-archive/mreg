<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Tree
 */

namespace mreg\Tree;


/**
 * Internal node class
 *
 * @package mreg\Tree
 */
class Node
{

    /**
     * The node id
     *
     * @var int
     */
    private $_id;

    
    /**
     * Parent node id. Null if node is root.
     *
     * @var int
     */
    private $_parentId;


    /**
     * Ids of child nodes
     *
     * @var array
     */
    private $_childrenIds;

    
    /**
     * Data associated with node
     *
     * @var mixed
     */
    private $_data;


    /**
     * Set node data
     *
     * @param int $id
     *
     * @param int $parentId
     *
     * @param array $childrenIds
     *
     * @param mixed $data
     */
    public function __construct(
        $id,
        $parentId,
        array $childrenIds = array(),
        $data = ''
    )
    {
        assert('is_int($id)');
        assert('is_int($parentId)');
        $this->_id = $id;
        $this->_parentId = $parentId;
        $this->_childrenIds = $childrenIds;
        $this->_data = $data;
    }


    /**
     * Add a child id to the list of children
     *
     * @param int $childId
     *
     * @return void
     */
    public function addChild($childId)
    {
        $this->_childrenIds[] = $childId;
        $this->_childrenIds = array_unique($this->_childrenIds);
    }


    /**
     * Check if this is a root node
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->_parentId == $this->_id;
    }


    /**
     * Check if this is a leaf node
     *
     * @return bool
     */
    public function isLeaf()
    {
        return empty($this->_childrenIds);
    }


    /**
     * Get node id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Get parent node id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->_parentId;
    }


    /**
     * Get list of child node ids
     *
     * @return array
     */
    public function getChildrenIds()
    {
        return $this->_childrenIds;
    }


    /**
     * Get node data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

}