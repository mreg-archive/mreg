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

/**
 * Model postal addresses as of Swedish standard SS 613401:2011 ed. 3
 * 
 * @package phplibaddress\Composer
 */
class Sv extends AbstractComposer
{
    /**
     * Addresse breviator object
     *
     * @var Breviator
     */
    private $breviator;

    /**
     * Model postal addresses as of Swedish standard SS 613401:2011 ed. 3
     *
     * @param Breviator $breviator Addresse breviator object
     * @param Address $address Optional address object
     */
    public function __construct(Breviator $breviator, Address $address = null)
    {
        parent::__construct($address);
        $this->breviator = $breviator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressee()
    {
        $address = $this->getAddress();

        // Add legal status to name of organisation if possible
        $org = mb_substr($address->getOrganisationName(), 0, 36);
        if (mb_strlen("$org {$address->getLegalStatus()}") <= 36) {
            $org = trim("$org {$address->getLegalStatus()}");
        }

        // Construct addressee
        $lines = array(
            mb_substr($address->getOrganisationalUnit(), 0, 36),
            $this->breviator->concatenate(
                $address->getGivenName(),
                $address->getSurname(),
                $address->getForm()
            ),
            $org
        );

        return implode(self::LINE_SEPARATOR, array_filter($lines));
    }

    /**
     * {@inheritdoc}
     */
    public function getMailee()
    {
        $address = $this->getAddress();

        if ($address->getNameOfMailee() == '') {

            return '';
        }

        return trim(
            sprintf(
                "%s %s",
                $address->getMaileeRoleDescriptor(),
                $address->getNameOfMailee()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLocality()
    {
        $address = $this->getAddress();

        if ($address->isDomestic()) {

            return trim(
                sprintf(
                    "%s %s",
                    $address->getPostcode(),
                    $address->getTown()
                )
            );
        } else {

            return trim(
                sprintf(
                    "%s-%s %s%s%s",
                    $address->getCountryCode(),
                    $address->getPostcode(),
                    $address->getTown(),
                    self::LINE_SEPARATOR,
                    $address->getCountry()
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getServicePoint()
    {
        $address = $this->getAddress();

        if (!$address->isServicePoint()) {

            return '';
        } else {

            return trim(
                sprintf(
                    "%s %s",
                    $address->getDeliveryService(),
                    $address->getAlternateDeliveryService()
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryLocation()
    {
        $address = $this->getAddress();

        if (!$address->isDeliveryLocation()) {

            return '';
        }

        $parts = array(
            $address->getThoroughfare(),
            $address->getPlot(),
            $address->getLittera(),
            $address->getStairwell()
        );

        if ($address->getDoor() != '') {
            $parts[] = "lgh {$address->getDoor()}";
        } else {
            $parts[] = $address->getFloor();
        }

        $parts = array_filter($parts);

        // If longer than 36 characters break up into two lines
        if (mb_strlen(implode(' ', $parts)) > 36) {
            $lines = array(
                array_shift($parts),
                implode(' ', $parts)
            );

        } else {
            // Else include supplementary delivery point above the thoroughfare
            $lines = array(implode(' ', $parts));
            if ($address->getSupplementaryData() != '') {
                array_unshift($lines, $address->getSupplementaryData());
            }
        }

        return trim(implode(self::LINE_SEPARATOR, $lines));
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryPoint()
    {
        $address = $this->getAddress();

        if ($address->isServicePoint()) {
            $point = $this->getServicePoint();
        } else {
            $point = $this->getDeliveryLocation();
        }

        return trim(
            sprintf(
                "%s%s%s",
                $point,
                self::LINE_SEPARATOR,
                $this->getLocality()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function format()
    {
        $addr = array(
            $this->getAddressee(),
            $this->getMailee(),
            $this->getDeliveryPoint()
        );

        return implode(self::LINE_SEPARATOR, array_filter($addr));
    }
}
