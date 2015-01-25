<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\stb\Accounting\Account;
use mreg\Exception\EconomyException;

/**
 * Accounting channels. Mapps fixed mreg entities to bookkeeping accounts
 *
 * @package mreg\Economy
 */
class Channels
{
    /**
     * List of channels
     *
     * @var array
     */
    private $_channels = array();

    /**
     * Add a channel
     *
     * @param string $id
     * @param Account $account
     *
     * @return void
     */
    public function addChannel($id, Account $account)
    {
        assert('is_string($id)');
        $this->_channels[$id] = $account;
    }

    /**
     * Validate that channel exists
     *
     * @param string $id
     *
     * @return bool
     */
    public function exists($id)
    {
        assert('is_string($id)');
        return isset($this->_channels[$id]);
    }

    /**
     * Get channel account
     *
     * @param string $id
     *
     * @return Account
     *
     * @throws EconomyException if channel does not exist
     */
    public function getAccount($id)
    {
        if ( !$this->exists($id) ) {
            $msg = "Bokföringskanal '$id' finns inte.";
            throw new EconomyException($msg);
        }

        return $this->_channels[$id];
    }

    /**
     * Get list of all channels
     *
     * @return array
     */
    public function getChannels()
    {
        return $this->_channels;
    }
}
