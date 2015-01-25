<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Auth
 */

namespace mreg\Auth;

use mreg\Exception;
use Phpass\Hash;
use Phpass\Strength;


/**
 * Password creation and validation wrapper
 *
 * @package mreg\Auth
 */
class Password
{

    /**
     * Hasher object
     *
     * @var Hash
     */
    private $_hasher;


    /**
     * Password entropy calculator
     *
     * @var Strength
     */
    private $_strengthObj;


    /**
     * Internal password hash
     *
     * @var string
     */
    private $_hash = '';


    /**
     * Entropy point limit for string passwords
     *
     * @var integer
     */
    private $_strongLimit = 100;


    /**
     * Password creation and validation wrapper
     *
     * @param Hash     $hasher
     * @param Strength $strength
     */
    public function __construct(Hash $hasher, Strength $strength)
    {
        $this->_hasher = $hasher;
        $this->_strengthObj = $strength;
    }


    /**
     * Get password hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->_hash;
    }
    
    
    /**
     * Set password hash
     *
     * @param string $hash
     *
     * @return void
     */
    public function setHash($hash)
    {
        assert('is_string($hash)');
        $this->_hash = $hash;
    }


    /**
     * Check if password is a valid match
     *
     * @param  string  $password Cleartext password
     *
     * @return boolean
     */
    public function isMatch($password)
    {
        assert('is_string($password)');

        return $this->_hasher->checkPassword($password, $this->_hash);
    }


    /**
     * Check if password is considered strong
     *
     * @param  string  $password Cleartext password
     *
     * @return boolean
     */
    public function isStrong($password)
    {
        assert('is_string($password)');

        return $this->_strengthObj->calculate($password) >= $this->_strongLimit;
    }


    /**
     * Set new password
     *
     * @param string $password Cleartext password
     *
     * @return void
     *
     * @throws Exception If password is not considered strong
     */
    public function setPassword($password)
    {
        assert('is_string($password)');
        if (!$this->isStrong($password)) {
            $msg = "Lösenordet är för svagt. Tänk på att använda en blandning";
            $msg .= " av gemener&#44; versaler&#44; siffror och skiljetecken.";
            $msg .= " Antal tecken bör vara minst 10.";
            throw new Exception($msg);
        }

        $this->_hash = $this->_hasher->hashPassword($password);
    }


    /**
     * Set entropy point limit for strong passwords
     *
     * @param int $limit
     *
     * @return void
     */
    public function setStrongLimit($limit)
    {
        assert('is_int($limit)');
        $this->_strongLimit = $limit;
    }


    /**
     * Generate and set new  password
     *
     * @return string Generated cleartext password
     */
    public function generate()
    {
        $password = $this->getRandomChar();
        
        while (!$this->isStrong($password)) {
            $password .= $this->getRandomChar();
        }

        $this->setPassword($password);

        return $password;
    }


    /**
     * Get a radnom character from adapter generated string
     *
     * @return [type]
     */
    private function getRandomChar()
    {
        $adapter = $this->_hasher->getAdapter();
        $data = explode('$', $adapter->genSalt());
        end($data);
        $data = current($data);

        return $data[mt_rand(0, strlen($data)-1)];
    }

}