<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Aux
 */

namespace mreg\Model\Aux;

use itbz\httpio\Request;


/**
 * Mreg auxiliary model base class
 *
 * @package mreg\Model\Aux
 */
abstract class AuxModel extends \mreg\Model\Model
{

    /**
     * Parent table name
     *
     * @var string
     */
    private $_refTable;


    /**
     * Parent id
     *
     * @var string
     */
    private $_refId;


    /**
     * Entity name
     *
     * @var string
     */
    private $_name;


    /**
     * Get name of parent table
     *
     * @return string
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }


    /**
     * Set name of parent table
     *
     * @param string $refTable
     *
     * @return void
     */
    public function setRefTable($refTable)
    {
        assert('is_string($refTable)');
        $this->_refTable = $refTable;
    }


    /**
     * Get parent id
     *
     * @return string
     */
    public function getRefId()
    {
        return $this->_refId;
    }


    /**
     * Set parent id
     *
     * @param string $refId
     *
     * @return void
     */
    public function setRefId($refId)
    {
        assert('is_string($refId)');
        $this->_refId = $refId;
    }


    /**
     * Get name of this faction
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Set name of this faction
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        assert('is_string($name)');
        $this->_name = $name;
    }


    /**
     * Load data from mapper into model
     *
     * @param array $data
     *
     * @return void
     */
    public function load(array $data)
    {
        parent::load($data);

        if (isset($data['name'])) {
            $this->setName($data['name']);
        }
        if (isset($data['ref_table'])) {
            $this->setRefTable($data['ref_table']);
        }
        if (isset($data['ref_id'])) {
            $this->setRefId($data['ref_id']);
        }
    }


    /**
     * Get title for this entity
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getName();
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        return parent::export() + array(
            'name' => $this->getName()
        );
    }


    /**
     * Extract data for datastore
     *
     * @param int $context
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
        return array(
            'id' => $this->getId(),
            'tCreated' => $this->getCreated()->getTimestamp(),
            'tModified' => $this->getModified()->getTimestamp(),
            'etag' => $this->getEtag(),
            'modifiedBy' => $this->getModifiedBy(),
            'ref_table' => $this->getRefTable(),
            'ref_id' => $this->getRefId(),
            'name' => $this->getName()
        );
    }

}