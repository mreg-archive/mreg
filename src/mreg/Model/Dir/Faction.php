<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Dir
 */

namespace mreg\Model\Dir;

use itbz\stb\Banking\PlusGiro;
use itbz\stb\Banking\Bankgiro;
use mreg\NullObject\EmptyPlusGiro;
use mreg\NullObject\EmptyBankgiro;
use mreg\NullObject\NonPersistentString;
use itbz\httpio\Request;
use DateTime;

/**
 * A Faction is an internal organisational group
 *
 * @package mreg\Model\Dir
 */
class Faction extends DirModel
{

    /**
     * Name of this faction
     *
     * @var string
     */
    private $_name;


    /**
     * Id of parent to this faction
     *
     * @var string
     */
    private $_parentId = "1000";


    /**
     * Faction type descriptor
     *
     * @var string
     */
    private $_factionType = "ANNAN";


    /**
     * Description of faction
     *
     * @var string
     */
    private $_description;


    /**
     * Id of accountant of this faction
     *
     * @var string
     */
    private $_accountantId;


    /**
     * Plusgiro account for this faction
     *
     * @var PlusGiro
     */
    private $_plusgiro;


    /**
     * Bankgiro account for this faction
     *
     * @var Bankgiro
     */
    private $_bankgiro;
    
    
    /**
     * Clone plusgiro and bankgiro
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->_plusgiro = clone $this->getPlusgiro();
        $this->_bankgiro = clone $this->getBankgiro();
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
     * Get parent id of this faction
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->_parentId;
    }


    /**
     * Set parent if of this faction
     *
     * @param string $parentId
     * 
     * @return void
     */
    public function setParentId($parentId)
    {
        assert('is_string($parentId)');
        $this->_parentId = $parentId;
    }


    /**
     * Get type descriptor for this faction
     *
     * @return string
     */
    public function getType()
    {
        return 'type_faction_' . $this->_factionType;
    }


    /**
     * Set type descriptor for this faction
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type)
    {
        assert('is_string($type)');
        $this->_factionType = $type;
    }


    /**
     * Get description of faction
     *
     * @return string
     */
    public function getDescription()
    {
        if (!$this->_description) {
            
            return $this->getName();
        }

        return $this->_description;
    }


    /**
     * Set description of faction
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
     * Get accountant id for this faction
     *
     * @return string
     */
    public function getAccountantId()
    {
        return $this->_accountantId;
    }


    /**
     * Set id of accountant of this faction
     *
     * @param string $id Numeric string
     *
     * @return void
     */
    public function setAccountantId($id)
    {
        assert('is_numeric($id)');
        $this->_accountantId = $id;
    }


    /**
     * Get plusgiro account for this faction
     *
     * @return string
     */
    public function getPlusgiro()
    {
        if (!isset($this->_plusgiro)) {
            $this->_plusgiro = new EmptyPlusGiro();
        }

        return $this->_plusgiro;
    }


    /**
     * Set plusgiro account for this faction
     *
     * @param string $plusgiro
     *
     * @return void
     */
    public function setPlusgiro($plusgiro)
    {
        assert('is_string($plusgiro)');
        if (!empty($plusgiro)) {
            $this->_plusgiro = new PlusGiro($plusgiro);
        }
    }


    /**
     * Get bankgiro account for this faction
     *
     * @return string
     */
    public function getBankgiro()
    {
        if (!isset($this->_bankgiro)) {
            $this->_bankgiro = new EmptyBankgiro();
        }

        return $this->_bankgiro;
    }


    /**
     * Set bankgiro account for this faction
     *
     * @param string $bankgiro
     *
     * @return void
     */
    public function setBankgiro($bankgiro)
    {
        assert('is_string($bankgiro)');
        if (!empty($bankgiro)) {
            $this->_bankgiro = new Bankgiro($bankgiro);
        }
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

        if (isset($data['parentId'])) {
            $this->setParentId($data['parentId']);
        }
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }
        if (isset($data['type'])) {
            $this->setType($data['type']);
        }
        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }
        if (isset($data['accountantId'])) {
            $this->setAccountantId($data['accountantId']);
        }
        if (isset($data['plusgiro'])) {
            $this->setPlusgiro($data['plusgiro']);
        }
        if (isset($data['bankgiro'])) {
            $this->setBankgiro($data['bankgiro']);
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
     * Get array describing addresse
     *
     * @return array
     */
    public function getAddressee()
    {
        return array(
            'organisation_name' => $this->getName()
        );
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
            'parentId' => $this->getParentId(),
            'description' => $this->getDescription(),
            'notes' => $this->getNotes(),
            'accountantId' => $this->getAccountantId(),
            'plusgiro' => (string)$this->getPlusgiro(),
            'bankgiro' => (string)$this->getBankgiro(),
            'url' => $this->getUrl(),
            'avatar' => $this->getAvatar(),
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
            'parentId' => $this->getParentId(),
            'name' => $this->getName(),
            'type' => $this->_factionType,
            'description' => $this->getDescription(),
            'accountantId' => $this->getAccountantId(),
            'plusgiro' => $this->getPlusgiro(),
            'bankgiro' => $this->getBankgiro(),
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
        
        if ( $req->body->is('type') ) {
            $data['type'] = $req->body->get('type', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('parentId') ) {
            $data['parentId'] =
                $req->body->get('parentId', FILTER_SANITIZE_NUMBER_INT);
        }
        if ( $req->body->is('description') ) {
            $data['description'] =
                $req->body->get('description', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('notes') ) {
            $data['notes'] = $req->body->get('notes', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('url') ) {
            $data['url'] = $req->body->get('url', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('avatar') ) {
            $data['avatar'] = $req->body->get('avatar', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('plusgiro') ) {
            $data['plusgiro'] =
                $req->body->get('plusgiro', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('bankgiro') ) {
            $data['bankgiro'] =
                $req->body->get('bankgiro', FILTER_SANITIZE_STRING);
        }

        $this->load($data);
    }

}