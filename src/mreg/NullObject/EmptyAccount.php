<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;

/**
 * Bank account null object
 *
 * @package mreg\NullObject
 */
class EmptyAccount extends \itbz\stb\Banking\AbstractAccount
{
    /**
     * Bank account null object
     */
    public function __construct()
    {
        parent::__construct('');
    }

    /**
     * API minimal implementation
     *
     * @param string $nr
     *
     * @return bool
     */
    public function isValidClearing($nr)
    {
        return TRUE;
    }

    /**
     * API minimal implementation
     *
     * @param string $nr
     *
     * @return bool
     */
    public function isValidStructure($nr)
    {
        return TRUE;
    }

    /**
     * API minimal implementation
     *
     * @param string $clearing
     * @param string $nr
     *
     * @return bool
     */
    public function isValidCheckDigit($clearing, $nr)
    {
        return TRUE;
    }

    /**
     * Get string describing account type
     *
     * @return string
     */
    public function getType()
    {
        return "Empty account";
    }
}
