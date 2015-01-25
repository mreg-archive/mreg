<?php
/**
 * This file is part of the phplibaddress package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package phplibaddress\Composer
 */

namespace itbz\phplibaddress\Composer;

use itbz\phplibaddress\Address;
use itbz\phplibaddress\Exception;

/**
 * Compose complete addresses from address components
 *
 * @package phplibaddress\Composer
 */
abstract class AbstractComposer
{
    /**
     * Character used to separate lines
     */
    const LINE_SEPARATOR = "\n";

    /**
     * Internal address container
     *
     * @var Address
     */
    private $address;

    /**
     * Get formatted address
     *
     * @return string
     */
    abstract public function format();

    /**
     * Get addresse (recipient)
     *
     * @return string
     */
    abstract public function getAddressee();

    /**
     * Get mailee (including role descriptor)
     * 
     * @return string
     */
    abstract public function getMailee();

    /**
     * Get formatted locality
     *
     * If the address is not domestic country code and name are included.
     *
     * @return string
     */
    abstract public function getLocality();

    /**
     * Get administrative service point address
     *
     * Eg a box or poste restante address
     *
     * @return string Returns the empty string if address->isServicePoint()
     * returns FALSE
     */
    abstract public function getServicePoint();

    /**
     * Get geographical address location
     *
     * Eg. street, apartment number and so on.
     *
     * @return string
     */
    abstract public function getDeliveryLocation();

    /**
     * Get the delivery point address.
     *
     * Can be an administrative address (service point) or a geographical
     * address (delivery location). Locality is always included in the deilvery
     * point address.
     *
     * @return string
     */
    abstract public function getDeliveryPoint();

    /**
     * Optionally load address container at construct
     *
     * @param Address $address
     */
    public function __construct(Address $address = null)
    {
        if ($address) {
            $this->setAddress($address);
        }
    }

    /**
     * Deep clone address container
     *
     * @return void
     */
    public function __clone()
    {
        if (isset($this->address)) {
            $this->address = clone $this->address;
        }
    }

    /**
     * Set address container
     *
     * @param Address $address
     *
     * @return void
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Get internal address container
     *
     * @return Address
     */
    public function getAddress()
    {
        if (!isset($this->address)) {
            $msg = "No address loaded.";
            throw new Exception($msg);
        }

        return $this->address;
    }

    /**
     * Check if address is syntactically valid
     * 
     * Returns false id address contains more than 6 lines
     * or any line is longer than 36 characters
     * 
     * @return bool
     */
    public function isValid()
    {
        $addr = explode(self::LINE_SEPARATOR, $this->format());
        if (count($addr) > 6) {

            return false;
        }

        foreach ($addr as $line) {
            if (mb_strlen($line) > 36) {

                return false;
            }
        }

        return true;
    }

    /**
     * Get formatted and sanitized address
     * 
     * Force address to contain no more than 6 lines
     * and no line that is longer than 36 characters
     * 
     * @return bool
     */
    public function getValid()
    {
        $addr = explode(self::LINE_SEPARATOR, $this->format());

        // Remove lines if more than 6
        while (count($addr) > 6) {
            array_shift($addr);
        }

        // Force line lengths
        foreach ($addr as &$line) {
            $line = mb_substr($line, 0, 36);
        }

        return implode(self::LINE_SEPARATOR, $addr);
    }
}
