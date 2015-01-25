<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\stb\Utils\Amount;
use mreg\Exception\EconomyException;

/**
 * Debit class null object
 *
 * @package mreg\Economy
 */
class BlancoClass extends DebitClass
{
    /**
     * Debit class null object
     */
    public function __construct()
    {
    }

    /**
     * Blanco name is always empty
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * The blanco charge is 0
     *
     * @return Amount
     */
    public function getCharge()
    {
        return new Amount('0');
    }

    /**
     * Blanco class has no associated template
     *
     * @return string Always the empty string
     */
    public function getTemplateName()
    {
        return '';
    }

    /**
     * Blanco interval ranges from 0 to 0
     *
     * @return array
     */
    public function getInterval()
    {
        return array(new Amount('0'), new Amount('0'));
    }
}
