<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model
 */

namespace mreg\Model;

use itbz\httpio\Request;
use DateTime;
use DateTimeZone;
use mreg\NullObject\NullDate;


/**
 * Mreg access model
 *
 * Manages owner, group and mode fields
 *
 * @package mreg\Model
 */
abstract class AcModel extends Model
{

    /**
     * Name of owner
     *
     * @var string
     */
    private $_owner;


    /**
     * Name of access group
     *
     * @var string
     */
    private $_group;


    /**
     * Access mode
     *
     * @var string
     */
    private $_mode;


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
        
        if (isset($data['owner'])) {
            $this->setOwner($data['owner']);
        }
        if (isset($data['group'])) {
            $this->setGroup($data['group']);
        }
        if (isset($data['mode'])) {
            $this->setMode($data['mode']);
        }
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
        return parent::extract($context, $using) + array_filter(
            array(
                'owner' => $this->getOwner(),
                'group' => $this->getGroup(),
                'mode' => $this->getMode()
            )
        );
    }
    

    /**
     * Get name of entity owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->_owner;
    }


    /**
     * Set name of entity owner
     *
     * @param string $owner
     *
     * @return void
     */
    public function setOwner($owner)
    {
        assert('is_string($owner)');
        $this->_owner = $owner;
    }


    /**
     * Get name of entity group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->_group;
    }


    /**
     * Set name of entity group
     *
     * @param string $group
     *
     * @return void
     */
    public function setGroup($group)
    {
        assert('is_string($group)');
        $this->_group = $group;
    }


    /**
     * Get entity access mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }


    /**
     * Set entity access mode
     *
     * @param string $mode
     *
     * @return void
     */
    public function setMode($mode)
    {
        assert('is_string($mode)');
        $this->_mode = $mode;
    }

}