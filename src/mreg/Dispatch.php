<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

use Aura\Router\Map;
use Aura\Router\Route;
use itbz\httpio\Request;
use mreg\Auth\Session;
use mreg\Model\Sys\User;


/**
 * Mreg route
 *
 * Each http iteration creates a dispatch object for the request and sends it
 * to the controller.
 *
 * @package mreg
 */
class Dispatch
{

    /**
     * Settings
     *
     * @var Settings
     */
    private $_settings;


    /**
     * Aura route object
     *
     * @var Route
     */
    private $_route;


    /**
     * Aura map object
     *
     * @var Map
     */
    private $_map;


    /**
     * Request object
     *
     * @var Request
     */
    private $_request;


    /**
     * Session object
     *
     * @var Session
     */
    private $_session;


    /**
     * Authenticated user
     *
     * @var User
     */
    private $_user;


    /**
     * Mreg dispatch object
     *
     * @param Settings $settings
     * @param Route $route
     * @param Map $map
     * @param Request $request
     * @param Session $session
     * @param User $user
     */
    public function __construct(
        Settings $settings,
        Route $route,
        Map $map,
        Request $request,
        Session $session,
        User $user
    )
    {
        $this->_settings = $settings;
        $this->_route = $route;
        $this->_map = $map;
        $this->_request = $request;
        $this->_session = $session;
        $this->_user = $user;
    }


    /**
     * Get settings
     *
     * @return Settings
     */    
    public function getSettings()
    {
        return $this->_settings;
    }


    /**
     * Get route
     *
     * @return Route
     */    
    public function getRoute()
    {
        return $this->_route;
    }


    /**
     * Get map
     *
     * @return Map
     */    
    public function getMap()
    {
        return $this->_map;
    }


    /**
     * Get request
     *
     * @return Request
     */    
    public function getRequest()
    {
        return $this->_request;
    }


    /**
     * Get session
     *
     * @return Session
     */    
    public function getSession()
    {
        return $this->_session;
    }


    /**
     * Get user
     *
     * @return User
     */    
    public function getUser()
    {
        return $this->_user;
    }

}