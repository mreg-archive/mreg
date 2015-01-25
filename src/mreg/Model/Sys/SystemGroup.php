<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Sys
 */

namespace mreg\Model\Sys;

use itbz\httpio\Request;
use mreg\Exception\InvalidContentException;


/**
 * Models a system user group
 *
 * @package mreg\Model\Sys
 */
class SystemGroup extends \mreg\Model\AcModel
{

    /**
     * Name of system group
     *
     * @var string
     */
    private $_name;
    
    
    /**
     * Group description
     *
     * @var string
     */
    private $_description = '';


    /**
     * Set name of system group
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        assert('is_string($name)');
        if (strlen($name) > 10) {
            $msg = "Namn på systemgrupp kan inte vara längre än 10 tecken";
            throw new InvalidContentException($msg);
        }
        $name = str_replace(' ', '-', $name);
        $this->_name = $name;
    }


    /**
     * Get name of system group
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Set description of system group
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        assert('is_string($description)');
        $this->_description = $description;
    }


    /**
     * Get description of system group
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
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
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
    }


    /**
     * Get title for this system group
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getName();
    }


    /**
     * Get type descriptor
     *
     * @return string
     */
    public function getType()
    {
        return 'type_sysgroup';
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        return parent::export() + array(
            'name' => $this->getName(),
            'description' => $this->getDescription(),
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
        return parent::extract($context, $using) + array(
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        );
    }


    /**
     * Load data from request
     *
     * @param Request $req
     *
     * @return void
     */
    public function loadRequest(Request $req)
    {
        $data = array();

        $data['name'] = $req->body->get('name', FILTER_SANITIZE_STRING);
        
        if ( $req->body->is('description') ) {
            $data['description'] = $req->body->get(
                'description',
                FILTER_SANITIZE_STRING
            );
        }
        
        $this->load($data);
    }

}