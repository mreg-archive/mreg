<?php
/**
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package utils
 */

namespace itbz\utils;

/**
 * Parse custom php.ini-like files and apply/verify server settings
 *
 * @package utils
 */
class PhpIniManager
{
    /**
     * List of verification or apply errors
     *
     * @var array
     */
    private $errors = array();

    /**
     * Return list of errors from last action
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Verify php settings from file
     *
     * File is parsed using parse_ini_file. Normal php.ini syntax is used.
     *
     * @param string $fname
     *
     * @return bool TRUE on success, FALSE otherwise
     */
    public function verifyFile($fname)
    {
        return $this->verify($this->parseFile($fname));
    }

    /**
     * Apply php settings from file
     *
     * File is parsed using parse_ini_file. Normal php.ini syntax is used.
     *
     * @param string $fname
     *
     * @return bool TRUE on success, FALSE otherwise
     */
    public function applyFile($fname)
    {
        return $this->apply($this->parseFile($fname));
    }

    /**
     * Verify php settings
     *
     * @param array $settings Associative array of settings
     *
     * @return bool TRUE of success, FALSE otherwise
     */
    public function verify(array $settings)
    {
        $this->errors = array();
        $format = "PHP setting '%s' should be '%s' but is '%s'";
        foreach ($settings as $name => $new) {
            $old = ini_get($name);
            if ($old != $new) {
                $this->errors[] = sprintf($format, $name, $new, $old);
            }
        }

        return empty($this->errors);
    }

    /**
     * Apply php settings
     *
     * @param array $settings Associative array of settings
     *
     * @return bool TRUE of success, FALSE otherwise
     */
    public function apply(array $settings)
    {
        $this->errors = array();
        $format = "Unable to set PHP setting '%s' to '%s', is '%s'";
        foreach ($settings as $name => $new) {
            $old = ini_get($name);
            if ($old != $new && ini_set($name, $new) === false) {
                $this->errors[] = sprintf($format, $name, $new, $old);
            }
        }

        return empty($this->errors);
    }

    /**
     * Parse ini file
     *
     * @param string $fname
     *
     * @return array
     *
     * @throws Exception if unable to read or parse file
     */
    private function parseFile($fname)
    {
        assert('is_string($fname)');
        if (!is_readable($fname)) {
            throw new Exception("Unable to read '$fname'");
        }
        $settings = @parse_ini_file($fname);
        if (!is_array($settings)) {
            $error = error_get_last();
            throw new Exception($error['message']);
        }

        return $settings;
    }
}
