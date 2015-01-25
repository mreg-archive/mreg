<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;

use itbz\stb\Banking\PlusGiro;
use itbz\datamapper\IgnoreAttributeInterface;

/**
 * Null object to represent void PlusGiro
 *
 * @package mreg\NullObject
 */
class EmptyPlusGiro extends PlusGiro implements IgnoreAttributeInterface
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
