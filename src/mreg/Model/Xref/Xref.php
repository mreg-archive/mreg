<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Xref
 */

namespace mreg\Model\Xref;

use itbz\datamapper\ModelInterface;
use mreg\NullObject\NullDate;
use DateTime;
use DateTimeZone;

/**
 * Db cross reference model
 *
 * @package mreg\Model\Xref
 */
class Xref implements ModelInterface
{

    /**
     * Xref id
     *
     * @var scalar
     */
    private $_id;


    /**
     * Xref since time
     *
     * @var DateTime
     */
    private $_since;


    /**
     * Xref unto time
     *
     * @var DateTime
     */
    private $_unto;


    /**
     * Xref master id
     *
     * @var scalar
     */
    private $_masterId;


    /**
     * Xref foreign id
     *
     * @var scalar
     */
    private $_foreignId;


    /**
     * Xref state
     *
     * @var string
     */
    private $_state = '';


    /**
     * Xref state comment
     *
     * @var string
     */
    private $_stateComment = '';


    /**
     * Timezone for all model datetimes
     *
     * @var DateTimeZone
     */
    private $_timezone;


    /**
     * Clone since and unto
     *
     * @return void
     */
    public function __clone()
    {
        $this->_since = clone $this->getSince();
        $this->_unto = clone $this->getUnto();
    }


    /**
     * Set xref id
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
     * Get xref id
     *
     * @return scalar
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Set xref since
     *
     * Sets since timezone to model timezone
     *
     * @param DateTime $since
     *
     * @return void
     */
    public function setSince(DateTime $since)
    {
        $this->_since = $since;
        $this->_since->setTimezone($this->getTimezone());
    }


    /**
     * Set xref since from timestamp
     *
     * @param string/int $timestamp
     *
     * @return void
     */
    public function setSinceTimestamp($timestamp)
    {
        if (!empty($timestamp) && $timestamp != "Datum saknas") {
            $this->setSince(new DateTime('@' . $timestamp));
        }
    }


    /**
     * Get xref since time
     *
     * @return DateTime
     */
    public function getSince()
    {
        if (isset($this->_since)) {

            return $this->_since;
        }

        return new NullDate();
    }


    /**
     * Get xref since time as timestamp
     *
     * @return int
     */
    public function getSinceTimestamp()
    {
        return $this->getSince()->getTimestamp();
    }


    /**
     * Set xref unto
     *
     * Sets unto timezone to model timezone
     *
     * @param DateTime $unto
     *
     * @return void
     */
    public function setUnto(DateTime $unto)
    {
        $this->_unto = $unto;
        $this->_unto->setTimezone($this->getTimezone());
    }


    /**
     * Set xref unto from timestamp
     *
     * @param string/int $timestamp
     *
     * @return void
     */
    public function setUntoTimestamp($timestamp)
    {
        if (!empty($timestamp) && $timestamp != "Datum saknas") {
            $this->setUnto(new DateTime('@' . $timestamp));
        }
    }


    /**
     * Get xref unto time
     *
     * @return DateTime
     */
    public function getUnto()
    {
        if (isset($this->_unto)) {

            return $this->_unto;
        }

        return new NullDate();
    }


    /**
     * Get xref unto time as timestamp
     *
     * @return int
     */
    public function getUntoTimestamp()
    {
        return $this->getUnto()->getTimestamp();
    }


    /**
     * Set xref master id
     *
     * @param scalar $id
     *
     * @return void
     */
    public function setMasterId($id)
    {
        assert('is_scalar($id)');
        $this->_masterId = $id;
    }


    /**
     * Get xref master id
     *
     * @return scalar
     */
    public function getMasterId()
    {
        return $this->_masterId;
    }


    /**
     * Set xref foreign id
     *
     * @param scalar $id
     *
     * @return void
     */
    public function setForeignId($id)
    {
        assert('is_scalar($id)');
        $this->_foreignId = $id;
    }


    /**
     * Get xref foreign id
     *
     * @return scalar
     */
    public function getForeignId()
    {
        return $this->_foreignId;
    }


    /**
     * Set xref state
     *
     * @param string $id
     *
     * @return void
     */
    public function setState($id)
    {
        assert('is_string($id)');
        $this->_state = $id;
    }


    /**
     * Get xref state
     *
     * @return string
     */
    public function getState()
    {
        return $this->_state;
    }


    /**
     * Conditionally set new state if current state equals old state
     *
     * @param string $oldState
     * @param string $newState
     *
     * @return bool TRUE if state was changed, FALSE if not
     */
    public function condSetState($oldState, $newState)
    {
        assert('is_string($oldState)');
        assert('is_string($newState)');
        if ($oldState == $this->getState()) {
            $this->setState($newState);

            return TRUE;
        }

        return FALSE;
    }


    /**
     * Set new state if current state equals 'OK'
     *
     * @param string $newState
     * @param string $comment Optionl string describing state change
     * @param DateTime $unto Custom unto value
     *
     * @return bool TRUE if state was changed, FALSE if not
     */
    public function deactivate($newState, $comment = '', DateTime $unto = NULL)
    {
        if (!$this->condSetState('OK', $newState)) {

            return FALSE;
        }
        $this->setStateComment($comment);
        if (!is_null($unto)) {
            $this->setUnto($unto);
        }

        return TRUE;
    }


    /**
     * Set xref state comment
     *
     * @param string $comment
     *
     * @return void
     */
    public function setStateComment($comment)
    {
        assert('is_string($comment)');
        $this->_stateComment = $comment;
    }


    /**
     * Get xref state comment
     *
     * @return string
     */
    public function getStateComment()
    {
        return $this->_stateComment;
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
            'tSince' => $this->getSinceTimestamp(),
            'tUnto' => $this->getUntoTimestamp(),
            'master_id' => $this->getMasterId(),
            'foreign_id' => $this->getForeignId(),
            'state' => $this->getState(),
            'stateComment' => $this->getStateComment(),
        );
    }


    /**
     * Export data to array
     *
     * @return array
     */
    public function export()
    {
        return array(
            'tSince' => $this->getSince()->format(DateTime::ATOM),
            'tUnto' => $this->getUnto()->format(DateTime::ATOM),
            'state' => $this->getState(),
            'stateComment' => $this->getStateComment(),
        );
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
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
        if (isset($data['tUnto'])) {
            $this->setUntoTimestamp($data['tUnto']);
        }
        if (isset($data['tSince'])) {
            $this->setSinceTimestamp($data['tSince']);
        }
        if (isset($data['master_id'])) {
            $this->setMasterId($data['master_id']);
        }
        if (isset($data['foreign_id'])) {
            $this->setForeignId($data['foreign_id']);
        }
        if (isset($data['state'])) {
            $this->setState($data['state']);
        }
        if (isset($data['stateComment'])) {
            $this->setStateComment($data['stateComment']);
        }
    }

}