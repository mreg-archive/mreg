<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Log
 */

namespace mreg\Log;


/**
 * Add user name to log record
 *
 * @package mreg\Log
 */
class UserProcessor
{

    /**
     * User name
     *
     * @var string
     */
    private $_user;


    /**
     * Add user name to log record
     *
     * @param string $user
     */
    public function __construct($user)
    {
        assert('is_string($user)');
        $this->_user = $user;
    }


    /**
     * Process record
     *
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['user'] = $this->_user;

        return $record;
    }

}