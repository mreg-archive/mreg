<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Auth
 */

namespace mreg\Auth;

use mreg\NullObject\AnonymousUser;
use mreg\Model\Sys\User;
use mreg\Mapper\UserMapper;
use itbz\datamapper\exception\DataNotFoundException;


/**
 * Build the authenticated user
 *
 * @package mreg\Auth
 */
class AuthUserBuilder
{

    /**
     * Session wrapper
     *
     * @var Session
     */
    private $_session;


    /**
     * User mapper
     *
     * @var UserMapper
     */
    private $_mapper;


    /**
     * User name to use with fake authentication
     *
     * @var string
     */
    private $_fakeUserName;


    /**
     * Request fingerprint
     *
     * @var string
     */
    private $_fingerprint = '';


    /**
     * Fingerprint created on successful authentication
     *
     * @var string
     */
    private $_newFingerprint = '';


    /**
     * Request user agent
     *
     * @var string
     */
    private $_userAgent = '';


    /**
     * Authorization credentials
     *
     * @param array
     */
    private $_credentials;


    /**
     * Flag if each user can only have one active session
     *
     * @var bool
     */
    private $_bSingleSession = TRUE;


    /**
     * Error message
     *
     * @var string
     */
    private $_error = '';


    /**
     * Build the authenticated user
     *
     * @param Session $session
     * @param UserMapper $mapper
     */
    public function __construct(Session $session, UserMapper $mapper)
    {
        $this->_session = $session;
        $this->_mapper = $mapper;

        // Bootstrap issue, root must read the first user
        $this->_mapper->setUser('root');
    }


    /**
     * Enable fake authentication
     *
     * @param string $user Name if fake user
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function enableFakeAuth($user = 'root')
    {
        assert('is_string($user)');
        $this->_fakeUserName = $user;
        
        return $this;
    }


    /**
     * Disable fake authentication
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function disableFakeAuth()
    {
        unset($this->_fakeUserName);
        
        return $this;
    }


    /**
     * Set request fingerprint
     *
     * @param string $fingerprint
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function setFingerprint($fingerprint)
    {
        assert('is_string($fingerprint)');
        $this->_fingerprint = $fingerprint;

        return $this;
    }


    /**
     * Set request user agent
     *
     * The user agent is stored as a non-cryptographic hash to get a fixed
     * length value
     *
     * @param string $userAgent
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function setUserAgent($userAgent)
    {
        assert('is_string($userAgent)');
        $this->_userAgent = hash('crc32', $userAgent);
        
        return $this;
    }


    /**
     * Set request authorization header
     *
     * @param string $authHeader
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function setAuthHeader($authHeader)
    {
        assert('is_string($authHeader)');
        $uname = '';
        $pswd = '';
        if (strpos($authHeader, ' ') !== FALSE) {
            list( , $encoded) = explode(' ', $authHeader, 2);
            $cleartxt = base64_decode($encoded);
            if (strpos($cleartxt, ':') !== FALSE) {
                list($uname, $pswd) = explode(':', $cleartxt, 2);
                $uname = filter_var($uname, FILTER_SANITIZE_STRING);
                $pswd = filter_var($pswd, FILTER_SANITIZE_STRING);
            }
        }
        $this->_credentials = array($uname, $pswd);

        return $this;
    }


    /**
     * Set if single sessions should be enforced
     *
     * @param bool $flag
     *
     * @return AuthUserBuilder Instance for chaining
     */
    public function setSingleSession($flag)
    {   
        assert('is_bool($flag)');
        $this->_bSingleSession = $flag;
        
        return $this;
    }


    /**
     * Build user
     *
     * @return User
     */
    public function getUser()
    {
        // Fake authentication
        if (isset($this->_fakeUserName)) {

            return $this->fetchUserFromDb($this->_fakeUserName);
        }

        // Already logged in
        if ($this->_session->getUser() != '') {
            $this->setError($this->_session->getError());
            try {
                if (
                    !$this->isError()
                    && $this->_userAgent == $this->_session->get('agent')
                    && $this->_fingerprint == $this->_session->get('fp')
                ) {

                    return $this->fetchUserFromDb($this->_session->getUser());
                }
            } catch (\mreg\Exception $e) {
            }
        }

        // No authentication
        if (!isset($this->_credentials)) {

            return new AnonymousUser();
        }

        // Perform authentication
        list($userName, $pswd) = $this->_credentials;
        $user = $this->fetchUserFromDb($userName);
        if (!$user->isValidPassword($pswd)) {
            $this->_mapper->tickInvalidAuth($user);
            
            return new AnonymousUser();
        }
        
        // Enforce single session
        if ($this->_bSingleSession) {
            $msg = "Du loggades ut därför att samma användarnamn loggade in på"
                   . " en annan dator. Kontakta sysadmin om du misstänker att"
                   . " någon kommit åt dina inloggningsuppgifter.";
            $this->_session->setUserError($userName, $msg);
        }

        // Set session values
        $this->_session->regenerate();
        $this->_session->setUser($userName);
        $this->_session->setError('');
        $this->_session->set('agent', $this->_userAgent);
        $this->_mapper->tickValidAuth($user);

        // Check if there is any internal user error
        if ($msg = $user->getStatusDesc()) {
            $this->setError($msg);

            return new AnonymousUser();
        }

        // Display user warnings
        if ($msg = $this->_mapper->getWarning()) {
            $this->setError($msg);
        }

        $this->generateNewFingerprint();

        return $user;
    }


    /**
     * Check if an error occured
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->_error != '';
    }


    /**
     * Get error message
     *
     * @return string Empty if no error occurred
     */
    public function getError()
    {
        return $this->_error;
    }


    /**
     * Set error message
     *
     * @param string $error
     */
    public function setError($error)
    {
        assert('is_string($error)');
        $this->_error = $error;
    }


    /**
     * Get fingerprint created on successful auth
     *
     * @return string
     */
    public function getNewFingerprint()
    {
        return $this->_newFingerprint;
    }


    /**
     * Generate a new fingerprint
     *
     * @return void
     */
    private function generateNewFingerprint()
    {
        $this->_newFingerprint = hash('sha512', uniqid('', TRUE));
        $this->_session->set('fp', $this->_newFingerprint);
    }


    /**
     * Fetch user from database
     *
     * @param string $name
     *
     * @return User
     */
    private function fetchUserFromDb($name)
    {
        assert('is_string($name)');
        try {

            return $this->_mapper->findByPk($name);
        } catch (DataNotFoundException $e) {

            return new AnonymousUser();
        }
    }

}