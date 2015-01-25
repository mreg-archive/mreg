<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\NullObject
 */

namespace mreg\NullObject;


/**
 * The anonymous user
 *
 * @package mreg\NullObject
 */
class AnonymousUser extends \mreg\Model\Sys\User
{

    /**
     * Empty constructor
     */
    public function __construct()
    {
        $this->uname = "Anonymous user";
        $this->groups = array();
        $this->fullname = "Anonymous user";
    }


    /**
     * Always returns FALSE for anonymous user
     *
     * @param string $password
     *
     * @return bool
     */
    public function isValidPassword($password)
    {
        return FALSE;
    }

}