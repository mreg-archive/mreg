<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;


/**
 * Mreg settings carrier
 *
 * @package mreg
 */
class Settings
{

    /**
     * Current settings
     *
     * @var array
     */
    private $_settings = array();


    /**
     * Check if setting exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists($name)
    {
        assert('is_string($name)');
        return isset($this->_settings[$name]);
    }


    /**
     * Set setting
     *
     * @param string $name
     * @param scalar $value
     *
     * @return void
     */
    public function set($name, $value)
    {
        assert('is_string($name)');
        assert('is_scalar($value)');
        $this->_settings[$name] = $value;
    }


    /**
     * Remove setting
     *
     * @param string $name
     *
     * @return void
     */
    public function remove($name)
    {
        assert('is_string($name)');
        unset($this->_settings[$name]);
    }


    /**
     * Get setting
     *
     * @param string $name
     *
     * @return scalar
     *
     * @throws Exception if setting does not exist
     */
    public function get($name)
    {
        assert('is_string($name)');
        if ( !$this->exists($name) ) {
            $msg = "Setting '$name' does not exist";
            throw new Exception($msg);
        }
        return $this->_settings[$name];
    }


    /**
     * Get all current settings
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_settings;
    }


    /**
     * Set many settings at once
     *
     * @param array $settings
     *
     * @return void
     */
    public function setBulk(array $settings)
    {
        foreach ( $settings as $name => $value ) {
            $this->set($name, $value);
        }
    }


    /**
     * Clear all settings
     *
     * @return void
     */
    public function clear()
    {
        $this->_settings = array();
    }

}