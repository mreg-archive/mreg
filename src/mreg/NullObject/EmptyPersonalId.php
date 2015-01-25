<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;

use DateTime;

/**
 * PersonalId null object
 *
 * @package mreg\NullObject
 */
class EmptyPersonalId extends \itbz\stb\ID\PersonalId
{
    /**
     * PersonalId null object
     */
    public function __construct()
    {
        $this->setDate(new DateTime());
        $this->setCheckDigit('1');
        $this->setIndividualNr('111');
        $this->setDelimiter('-');
    }

    /**
     * Set id number
     *
     * Void
     *
     * @param string $id
     *
     * @return void
     */
    public function setId($id)
    {
    }

    /**
     * Get sex, always returns 'O'
     *
     * @return string 
     */
    public function getSex()
    {
        return 'O';
    }
}
