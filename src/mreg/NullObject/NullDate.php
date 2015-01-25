<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;

use itbz\datamapper\IgnoreAttributeInterface;

/**
 * Null object to represent void DateTime
 *
 * @package mreg\NullObject
 */
class NullDate extends \DateTime implements IgnoreAttributeInterface
{
    /**
     * Null timestamp is 0
     */
    public function __construct()
    {
        parent::__construct('@0');
    }

    /**
     * Send not set message
     *
     * @param string $format Ignored as this is a null date
     *
     * @return string
     */
    public function format($format)
    {
        return "Datum saknas";
    }
}
