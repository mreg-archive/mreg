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
 * Debit class
 *
 * @package mreg\Economy
 */
class DebitClass
{

    /**
     * Name of this class
     *
     * @var string
     */
    private $_name;
    

    /**
     * Charge for this class
     *
     * @var Amount
     */
    private $_charge;


    /**
     * Parent of this class
     *
     * @var DebitClass
     */
    private $_parent;


    /**
     * Income interval for this class
     *
     * @var array
     */
    private $_interval;


    /**
     * Name of template associated with this class
     *
     * @var string
     */
    private $_template;


    /**
     * Array af debits associated with this class
     *
     * @var array
     */
    private $_debits = array();


    /**
     * Set name, charge, template name and income interval
     *
     * @param string $name
     *
     * @param Amount $charge
     *
     * @param string $templateName
     *
     * @param Amount $intervalStart
     *
     * @param Amount $intervalEnd
     */
    public function __construct(
        $name,
        Amount $charge,
        $templateName,
        Amount $intervalStart,
        Amount $intervalEnd
    )
    {
        assert('is_string($name)');
        assert('is_string($templateName)');
        $this->_name = $name;
        $this->_charge = $charge;
        $this->_interval = array($intervalStart, $intervalEnd);
        $this->_template = $templateName;
    }


    /**
     * Get name of this class
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * Get charge for this class
     *
     * @return Amount
     */
    public function getCharge()
    {
        return $this->_charge;
    }


    /**
     * Get name of template associated with this class
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->_template;
    }


    /**
     * Get income interval for this class
     *
     * @return array
     */
    public function getInterval()
    {
        return $this->_interval;
    }


    /**
     * Check if amount is within interval
     *
     * TRUE if amount is greater or equal with interval start and lesser or
     * equal with interval end
     *
     * @param Amount $amount
     *
     * @return bool
     */
    public function isWithinInterval(Amount $amount)
    {
        $interval = $this->getInterval();
    
        return (
            !$amount->isLesserThan($interval[0])
            && !$amount->isGreaterThan($interval[1])
        );
    }


    /**
     * Set parent debit class
     *
     * @param DebitClass $parent
     *
     * @return void
     *
     * @throws EconomyException if parent interval does not match this interval
     */
    public function setParent(DebitClass $parent)
    {
        // Validate that interval conforms to parent interval
        $parentInterval = $parent->getInterval();
        $interval = $this->getInterval();
        if ( 
            ($interval[0]->isLesserThan($parentInterval[0]))
            || ($interval[1]->isGreaterThan($parentInterval[1]))
        ) {
            $msg = "Inkomstintervall för klass '{$this->getName()}' matchar ej";
            $msg .= " föräldraklassens ({$parent->getName()}) inkomstintervall";
            throw new EconomyException($msg);
        }
        // Stil here .. all OK
        $this->_parent = $parent;
    }


    /**
     * Add a debit to this class
     *
     * @param string $name
     *
     * @param Amount $amount
     *
     * @return void
     */
    public function addDebit($name, Amount $amount)
    {
        assert('is_string($name)');
        $this->_debits[$name] = $amount;
    }


    /**
     * Get array of debits of this class
     *
     * @return array
     */
    public function getDebits()
    {
        if ( isset($this->_parent) ) {
            $debits = $this->_parent->getDebits();
        } else {
            $debits = array();
        }
        return array_merge(
            $debits,
            $this->_debits,
            array('AVGIFT' => $this->getCharge())
        );
    }

}
