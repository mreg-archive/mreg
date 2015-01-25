<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model
 */

namespace mreg\Model;

use itbz\datamapper\ModelInterface;
use itbz\httpio\Request;
use DateTime;
use DateTimeZone;
use mreg\NullObject\NullDate;

/**
 * Mreg model base class
 *
 * @package mreg\Model
 */
abstract class Model implements ModelInterface
{

    /**
     * Entity id
     *
     * @var scalar
     */
    private $_id;


    /**
     * Entity creation time
     *
     * @var DateTime
     */
    private $_created;


    /**
     * Entity modification time
     *
     * @var DateTime
     */
    private $_modified;


    /**
     * Entity etag
     *
     * @var string
     */
    private $_etag;


    /**
     * Name of user that last modified entity
     *
     * @var string
     */
    private $_modifiedBy;

    
    /**
     * Timezone for all model datetimes
     *
     * @var DateTimeZone
     */
    private $_timezone;


    /**
     * Get entity type
     *
     * @return string
     */
    abstract public function getType();


    /**
     * Get title for this entity
     *
     * @return string
     */
    abstract public function getTitle();


    /**
     * Load data from request
     *
     * @param Request $request
     *
     * @return void
     */
    abstract public function loadRequest(Request $request);


    /**
     * Load data from mapper into model
     *
     * @param array $data
     *
     * @return void
     */
    public function load(array $data)
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['tCreated']) && ctype_digit($data['tCreated'])) {
            $this->setCreated(new DateTime('@' . $data['tCreated']));
        }
        if (isset($data['tModified']) && ctype_digit($data['tModified'])) {
            $this->setModified(new DateTime('@' . $data['tModified']));
        }
        if (isset($data['etag'])) {
            $this->setEtag($data['etag']);
        }
        if (isset($data['modifiedBy'])) {
            $this->setModifiedBy($data['modifiedBy']);
        }
    }


    /**
     * Export entity data to array
     *
     * @return array
     */
    public function export()
    {
        return array_filter(
            array(
                'id' => $this->getId(),
                'tCreated' => $this->getCreated()->format(DateTime::ATOM),
                'tModified' => $this->getModified()->format(DateTime::ATOM),
                'type' => $this->getType(),
                'title' => $this->getTitle()
            )
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
        return array_filter(
            array(
                'id' => $this->getId(),
                'tCreated' => $this->getCreated()->getTimestamp(),
                'tModified' => $this->getModified()->getTimestamp(),
                'etag' => $this->getEtag(),
                'modifiedBy' => $this->getModifiedBy()
            )
        );
    }


    /**
     * Clone created and modified
     *
     * @return void
     */
    public function __clone()
    {
        $this->_created = clone $this->getCreated();
        $this->_modified = clone $this->getModified();
    }


    /**
     * Set model timezone
     *
     * @param DateTimeZone $timezone
     *
     * @return void
     */
    public function setTimeZone(DateTimeZone $timezone)
    {
        $this->_timezone = $timezone;
    }


    /**
     * Get timezone
     *
     * If timezone is not set default Europe/Stockholm is returned
     *
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        if (!isset($this->_timezone)) {
            $this->setTimezone(new DateTimeZone('Europe/Stockholm'));
        }
        
        return $this->_timezone;
    }


    /**
     * Get entity id
     *
     * @return scalar
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Set entity id
     *
     * @param scalar $id
     *
     * @return void
     */
    public function setId($id)
    {
        assert('is_scalar($id)');
        $this->_id = $id;
    }


    /**
     * Get entity creation time
     *
     * @return DateTime
     */
    public function getCreated()
    {
        if (isset($this->_created)) {

            return $this->_created;
        }

        return new NullDate();
    }


    /**
     * Set entity creation datetime
     *
     * Sets created timezone to model timezone
     *
     * @param DateTime $created
     *
     * @return void
     */
    public function setCreated(DateTime $created)
    {
        $this->_created = $created;
        $this->_created->setTimezone($this->getTimezone());
    }


    /**
     * Get entity modification time
     *
     * @return DateTime
     */
    public function getModified()
    {
        if (isset($this->_modified)) {

            return $this->_modified;
        }

        return new NullDate();
    }


    /**
     * Set entity modified datetime
     *
     * Sets modified timezone to model timezone
     *
     * @param DateTime $modified
     *
     * @return void
     */
    public function setModified(DateTime $modified)
    {
        $this->_modified = $modified;
        $this->_modified->setTimezone($this->getTimezone());
    }


    /**
     * Get entity etag
     *
     * @return string
     */
    public function getEtag()
    {
        return $this->_etag;
    }


    /**
     * Set entity etag
     *
     * @param string $etag
     *
     * @return void
     */
    public function setEtag($etag)
    {
        assert('is_string($etag)');
        $this->_etag = $etag;
    }


    /**
     * Get name of user that last modified entity
     *
     * @return string
     */
    public function getModifiedBy()
    {
        return $this->_modifiedBy;
    }


    /**
     * Set name of user that last modified entity
     *
     * @param string $user
     *
     * @return void
     */
    public function setModifiedBy($user)
    {
        assert('is_string($user)');
        $this->_modifiedBy = $user;
    }

}