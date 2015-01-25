<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use mreg\Exception\EconomyException;
use itbz\stb\Utils\Amount;

/**
 * Debit classes for mreg factions
 *
 * @package mreg\Economy
 */
class TableOfDebits
{

    /**
     * Represents infinity for intervals with no upper limit
     */
    const INFINITY = PHP_INT_MAX;


    /**
     * Array of debit class data
     *
     * @var array
     */
    private $_rawClassData = array();

    
    /**
     * Array of generated DebitClasses. Is filled in generateClasses() using
     * data in $rawClassData
     *
     * @var array
     */
    private $_classes = array();


    /**
     * List of registered debits
     *
     * @var array
     */
    private $_debitNames = array();


    /**
     * Parent TableOfDebits object
     *
     * @var TableOfDebits
     */
    private $_parent;


    /**
     * Set parent TableOfDebits object.
     *
     * Parent must be completely created. If parent is altered, for example if
     * parent gets a new second parent, or if you change parent classes, parent
     * must be reloaded.
     *
     * @param TableOfDebits $parent
     *
     * @return void
     */
    public function setParent(TableOfDebits $parent)
    {
        $this->_parent = $parent;
        $this->generateClasses();
    }


    /**
     * Get parent TableOfDebits object
     *
     * @return TableOfDebits
     */
    public function getParent()
    {
        return $this->_parent;
    }


    /**
     * Add debit to table
     *
     * @param string $name
     *
     * @return void
     *
     * @throws EconomyException if name is not upper case alpha characters only
     */
    public function addDebitName($name)
    {
        if (!ctype_upper($name)) {
            $msg = "Kan ej lägga till avsättning:";
            $msg .= " endast versala bokstäver får användas";
            throw new EconomyException($msg);         
        }
        if (!$this->debitExists($name)) {
            $this->_debitNames[] = $name;
        }
    }


    /**
     * Get list of registered debit names
     *
     * @return array
     */
    public function getDebitNames()
    {
        return $this->_debitNames;
    }


    /**
     * Check if debit exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function debitExists($name)
    {
        return in_array($name, $this->_debitNames);
    }


    /**
     * Set class data
     *
     * @param string $name Name of class
     *
     * @param Amount $charge Charge for this class
     *
     * @param string $templateName Name of accounting template
     *
     * @param Amount $intervalStart Start of interval this class covers
     *
     * @param array $debits Associative array of debits. Keys represent debit
     * names, vales must be Amount objects.
     *
     * @return void
     *
     * @throws EconomyException if name does not match ^[A-Z]{1,2}\d{0-2}$
     *
     * @throws EconomyException if any $debits key is not upper case alpha
     *
     * @throws EconomyException if any $debits value is not an Amount object
     *
     * @throws EconomyException if unable to create DebitClass object
     */
    public function setClass(
        $name,
        Amount $charge,
        $templateName,
        Amount $intervalStart,
        array $debits = NULL
    )
    {
        assert('is_string($templateName)');
        if ( !$debits ) $debits = array();

        // Validate name        
        if (!preg_match("/^[A-Z]{1,2}\d{0,2}$/", $name)) {
            $msg = "Kan ej spara avgiftsklass '$name': ogiltigt namn";
            throw new EconomyException($msg);
        }
        
        // These values are required for all classes
        $base = array(
            'template' => $templateName,
            'interval_start' => $intervalStart,
            'charge' => $charge
        );

        // Validate debits and save debit names        
        foreach ($debits as $debitName => $amount) {
            if (!$amount instanceof Amount) {
                $msg = "Kan ej spara avgiftsklass '$name'";
                $msg .= ": ogiltig avsättning '$debitName'";
                throw new EconomyException($msg);
            }
            $this->addDebitName($debitName);
        }
        
        // Save class data
        $this->_rawClassData[$name] = array(
            'base' => $base,
            'debits' => $debits
        );

        // Generate new classes
        try {
            $this->generateClasses();
        } catch (EconomyException $e) {
            unset($this->_rawClassData[$name]);
            throw $e;
        }
    }


    /**
     * Check if class exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function classExists($name)
    {
        return isset($this->_classes[$name]);
    }


    /**
     * Remove class
     *
     * @param string $name
     *
     * @return void
     *
     * @throws EconomyException if umable to remove class
     */
    public function removeClass($name)
    {
        $data = $this->_rawClassData[$name];
        unset($this->_rawClassData[$name]);

        // Generate new classes
        try {
            $this->generateClasses();
        } catch (EconomyException $e) {
            $this->_rawClassData[$name] = $data;
            throw $e;
        }
    }


    /**
     * Remove debit from table
     *
     * @param string $name
     *
     * @return void
     */
    public function removeDebit($name)
    {
        assert('is_string($name)');
        $name = strtoupper($name);
        $index = array_search($name, $this->_debitNames);
        if ( $index !== FALSE ) {
            // Remove name of debit
            unset($this->_debitNames[$index]);
            // Remove amounts for this debit
            foreach ( $this->_rawClassData as &$data ) {
                unset($data['debits'][$name]);
            }
        }
        $this->generateClasses();
    }


    /**
     * Get array of DebitClass objects created from registered data
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }


    /**
     * Get debit class from name
     *
     * @param string $name
     *
     * @return DebitClass
     *
     * @throws EconomyException if class does not exist
     */
    public function getClass($name)
    {
        if (!$this->classExists($name)) {
            $msg = "Avgiftsklass '$name' finns inte";
            throw new EconomyException($msg);
        }

        return $this->_classes[$name];    
    }


    /**
     * Get debit class where salary is within the limits of interval
     *
     * @param Amount $salary
     *
     * @return DebitClass
     *
     * @throws EconomyException if no class is found
     */
    public function getClassFromSalary(Amount $salary)
    {
        foreach ($this->_classes as $class) {
            if ($class->isWithinInterval($salary)) {

                return $class;
            }
        }
        $msg = "Kan ej hitta avgiftsklass for lön '$salary'.";
        throw new EconomyException($msg);
    }


    /**
     * Get suitable debit class when paying $amount
     *
     * @param Amount $amount
     *
     * @return DebitClass
     *
     * @throws EconomyException if no class is found
     */
    public function getClassFromAmount(Amount $amount)
    {
        foreach ($this->_classes as $class) {
            if (!$amount->isLesserThan($class->getCharge())) {

                return $class;
            }
        }

        // Still here means $amount is below the lowest class
        return $this->getClassFromSalary(new Amount('0'));
    }


    /**
     * PHP magic on serialize method. Get list of preperties to serialize
     *
     * @return array
     */
    public function __sleep()
    {
        return array(
            '_rawClassData',
            '_debitNames'
        );
    }


    /**
     * PHP magic on unserialize method. Generate classes.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->generateClasses();
    }


    /**
     * Sort class data
     *
     * Sort the array of raw class data in order of class name. A sorts
     * before B. AA sorts before A. AA1 sorts before AA. AA1 sorts before AA2.
     *
     * @return void
     */
    private function sortRawClassData()
    {
        uksort(
            $this->_rawClassData,
            function($a, $b){
                foreach ( str_split($a) as $index => $char ) {
                    if ( !isset($b[$index]) ) return -1;
                    $compare = strcasecmp($char, $b[$index]);
                    if ( $compare == 0 ) continue;
                    return $compare;
                }
                return 1;
            }
        );
    }


    /**
     * Validate table
     *
     * To maintain interval consistency each table: 1) Must contain a class
     * starting its interval at 0. 2) Consecutive classes must have intervals
     * starting at a value greater than the preceding interval start value.
     * 3) Interval stop values are not specified, but are calculated using the
     * descendant interval start value (or are infinite if not descendant
     * exists).
     *
     * This ensures that 1) intervals never overlap, 2) there will never
     * exist values not covered by a table.
     *
     * @return void
     *
     * @throws EconomyException if intervals are invalid (overlapping or no 0)
     */
    private function validateIntervals()
    {
        if ( empty($this->_rawClassData) ) return;

        // Sort raw data before validating
        $this->sortRawClassData();

        // The lowest class must start its interval at 0
        $classData = array_reverse($this->_rawClassData);
        $first = current($classData);
        if (
            $first === FALSE
            || $first['base']['interval_start']->getFloat() != 0.0
        ) {
            $msg = "Den sista avgiftsklassen måste starta intervall på 0";
            throw new EconomyException($msg);
        }

        // Loop classes validating interval growth
        $precedingInterval = new Amount('0');
        foreach ( $classData as $name => $data ) {
            $interval = $data['base']['interval_start'];
            if (
                !$interval->isGreaterThan($precedingInterval)
                && $interval->getFloat() != 0.0
            ) {
                // Invalid interval, must be larger than preceding if not 0
                $msg = "Tabell innehåller överlappande intervaller. Se '$name'";
                throw new EconomyException($msg);
            }
            $precedingInterval = $interval;
        }
    }


    /**
     * Generate debits classes
     *
     * Generate DebitClass objects using $this->_rawClassData. Generated objects
     * are saved in $this->_classes
     *
     * @return void
     *
     * @throws EconomyException if class is missing in parent
     *
     * @throws EconomyException if class interval does not match parent interval
     */
    private function generateClasses()
    {
        $this->validateIntervals();     // Throws exception if invalid
        $this->_classes = array();      // Reset the classes array
        
        // Loop through raw data creating classes
        $intervalEnd = new Amount(self::INFINITY);
        foreach ( $this->_rawClassData as $name => $data ) {
            // Set interval end and generate class
            $data['base']['interval_end'] = $intervalEnd;
            $this->_classes[$name] = $this->generateClass($name, $data);
            // Calculate interval end for the next class
            $intervalEnd = clone $data['base']['interval_start'];
            if ( $intervalEnd->isGreaterThan(new Amount('0'))) {
                $intervalEnd->add(new Amount('-0.01'));
            }
        }
    }


    /**
     * Generate DebitClass using $data
     *
     * @param string $name
     *
     * @param array $data
     *
     * @return DebitClass
     *
     * @throws EconomyException if class does not exist in parent
     */
    private function generateClass($name, $data)
    {
        // Create DebitClass instance
        $class = new DebitClass(
            $name,
            $data['base']['charge'],
            $data['base']['template'],
            $data['base']['interval_start'],
            $data['base']['interval_end']
        );

        // Add debits to class
        foreach ( $this->_debitNames as $debitName ) {
            if ( isset($data['debits'][$debitName]) ) {
                $class->addDebit($debitName, $data['debits'][$debitName]);
            } else {
                $class->addDebit($debitName, new Amount('0'));
            }
        }

        // Set parent DebitClass using only alhpa part of class name
        if ( $parent = $this->getParent() ) {
            $name = preg_replace('/^([A-Za-z]+).*$/', '$1', $name);
            if ( !$parent->classExists($name) ) {
                $msg = "Avgiftsklass '$name' finns inte i förälder.";
                throw new EconomyException($msg);
            }
            $parentclass = $parent->getClass($name);
            $class->setParent($parentclass);
        }
        
        return $class;
    }

}
