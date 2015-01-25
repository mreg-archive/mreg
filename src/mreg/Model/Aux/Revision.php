<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Aux
 */

namespace mreg\Model\Aux;

use itbz\httpio\Request;
use DateTime;


/**
 * Revision model
 *
 * @package mreg\Model\Aux
 */
class Revision extends AuxModel
{

    /**
     * Table of referred field
     *
     * @var string
     */
    private $_refTable = '';


    /**
     * Column of referred field
     *
     * @var string
     */
    private $_refColumn = '';


    /**
     * Id of referred field
     *
     * @var string
     */
    private $_refId = '';
    
    
    /**
     * Old value
     *
     * @var string
     */
    private $_oldValue = '';
    

    /**
     * New value
     *
     * @var string
     */
    private $_newValue = '';


    /**
     * Set table of referred field
     *
     * @param string $ref
     *
     * @return void
     */
    public function setRefTable($ref)
    {
        assert('is_string($ref)');
        $this->_refTable = $ref;
    }


    /**
     * Get table of referred field
     *
     * @return string
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }


    /**
     * Set column of referred field
     *
     * @param string $ref
     *
     * @return void
     */
    public function setRefColumn($ref)
    {
        assert('is_string($ref)');
        $this->_refColumn = $ref;
    }


    /**
     * Get column of referred field
     *
     * @return string
     */
    public function getRefColumn()
    {
        return $this->_refColumn;
    }


    /**
     * Set id of referred field
     *
     * @param string $ref
     *
     * @return void
     */
    public function setRefId($ref)
    {
        assert('is_string($ref)');
        $this->_refId = $ref;
    }


    /**
     * Get id of referred field
     *
     * @return string
     */
    public function getRefId()
    {
        return $this->_refId;
    }


    /**
     * Set old field value
     *
     * @param string $value
     *
     * @return void
     */
    public function setOldValue($value)
    {
        assert('is_string($value)');
        $this->_oldValue = $value;
    }


    /**
     * Get old field value
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->_oldValue;
    }


    /**
     * Set new field value
     *
     * @param string $value
     *
     * @return void
     */
    public function setNewValue($value)
    {
        assert('is_string($value)');
        $this->_newValue = $value;
    }


    /**
     * Get new field value
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->_newValue;
    }
    

    /**
     * Get entity type
     *
     * @return string
     */
    public function getType()
    {
        return "type_revision";
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
        if (isset($data['ref_id'])) {
            $this->setRefId($data['ref_id']);
        }
        if (isset($data['ref_column'])) {
            $this->setRefColumn($data['ref_column']);
        }
        if (isset($data['ref_table'])) {
            $this->setRefTable($data['ref_table']);
        }
        if (isset($data['old_value'])) {
            $this->setOldValue($data['old_value']);
        }
        if (isset($data['new_value'])) {
            $this->setNewValue($data['new_value']);
        }
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        return array(
            'id' => $this->getId(),
            'tModified' => $this->getModified()->format('Y-m-d'),
            'modifiedBy' => $this->getModifiedBy(),
            'ref_table' => $this->getRefTable(),
            'ref_column' => $this->getRefColumn(),
            'ref_id' => $this->getRefId(),
            'old_value' => $this->getOldValue(),
            'new_value' => $this->getNewValue()
        );
    }


    /**
     * Load data from request
     *
     * Void because revsions are never edited
     *
     * @param Request $req
     *
     * @return void
     */
    public function loadRequest(Request $req)
    {
    }


    /**
     * Extract data for datastore
     *
     * Void because revisions are never edited
     *
     * @param int $context
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
    }

}