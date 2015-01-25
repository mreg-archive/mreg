<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;

use itbz\stb\Banking\Bankgiro;
use itbz\datamapper\IgnoreAttributeInterface;

/**
 * Null object to represent void Bankgiro
 *
 * @package mreg\NullObject
 */
class EmptyBankgiro extends Bankgiro implements IgnoreAttributeInterface
{
    /**
     * Override with empty constructor
     */
    public function __construct()
    {
    }

    /**
     * Send not set message
     *
     * @return string
     */
    public function __toString()
    {
        return "";
    }
}
