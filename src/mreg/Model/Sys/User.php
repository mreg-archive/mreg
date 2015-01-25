<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Sys
 */

namespace mreg\Model\Sys;

use itbz\httpio\Request;
use DateTime;
use mreg\NullObject\NullDate;
use mreg\Auth\Password;
use mreg\Exception;

/**
 * Models a system user
 *
 * @package mreg\Model\Sys
 */
class User extends \mreg\Model\Dir\DirModel
{

    /**
     * Status user active
     */
    const STATUS_VALID = 0;


    /**
     * Status user blocked due to inactivity.
     */
    const STATUS_INACTIVE = 1;


    /**
     * User blocked due to multiple misstaken logins.
     */
    const STATUS_MULTIPLE_ERRORS = 2;


    /**
     * User deactivated by admin
     */
    const STATUS_DEACTIVATED = 3;


    /**
     * User deactivated due to expired password
     */
    const STATUS_EXPIRED_PSWD = 4;


    /**
     * User name
     *
     * @var string
     */
    private $_uname;


    /**
     * Full name of user
     *
     * @var string
     */
    private $_fullname;


    /**
     * Password object
     *
     * @var Password
     */
    private $_password;


    /**
     * Time when password was created
     *
     * @var DateTime
     */
    private $_passwordCreated;


    /**
     * Array of user groups
     *
     * @var array
     */
    private $_groups;


    /**
     * User status indicator
     *
     * @var int
     */
    private $_status = 0;


    /**
     * Number of invlid auths for this user
     *
     * @var int
     */
    private $_invalidAuths;


    /**
     * Counter of user logins
     *
     * @var int
     */
    private $_logins;


    /**
     * Datetime of last login
     *
     * @var DateTime
     */
    private $_lastLogin;


    /**
     * Datetime of login before last
     *
     * @var DateTime
     */
    private $_loginBeforeLast;


    /**
     * Id of faction this user is accounting for
     *
     * @var string
     */
    private $_accountingFor;


    /**
     * Models a system user
     *
     * @param Password $password
     */
    public function __construct(Password $password)
    {
        $this->_password = $password;
    }


    /**
     * Clone internal objects
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();

        if (isset($this->_password)) {
            $this->_password = clone $this->_password;
        }
        if (isset($this->_lastLogin)) {
            $this->_lastLogin = clone $this->_lastLogin;
        }
        if (isset($this->_loginBeforeLast)) {
            $this->_loginBeforeLast = clone $this->_loginBeforeLast;
        }
    }


    /**
     * Load data from mapper into model
     *
     * @param array $data
     *
     * @return void
     */
    public function load(array $data)
    {
        parent::load($data);
        
        if (isset($data['fullname'])) {
            $this->setFullname($data['fullname']);
        }
        if (isset($data['uname'])) {
            $this->setName($data['uname']);
        }
        if (isset($data['password'])) {
            $this->setPasswordHash($data['password']);
        }
        if (isset($data['tPswdCreated'])) {
            $this->setPswdCreated($data['tPswdCreated']);
        }
        if (isset($data['groups'])) {
            $this->setGroups(explode(',', $data['groups']));
        }
        if (isset($data['status'])) {
            $this->setStatus(intval($data['status']));
        }
        if (isset($data['nInvalidAuths'])) {
            $this->setInvalidAuths(intval($data['nInvalidAuths']));
        }
        if (isset($data['nLogins'])) {
            $this->setLogins(intval($data['nLogins']));
        }
        if (isset($data['tLogin'])) {
            $this->setLastLogin($data['tLogin']);
        }
        if (isset($data['tLastLogin'])) {
            $this->setLoginBeforeLast($data['tLastLogin']);
        }
        if (isset($data['accountingFor'])) {
            $this->setAccountingFor($data['accountingFor']);
        }
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        return parent::export() + array(
            'uname' => $this->getName(),
            'fullname' => $this->getFullName(),
            'groups' => $this->getGroups(),
            'status'=> $this->getStatusDesc(),
            'invalidAuths' => $this->getInvalidAuths(),
            'validAuths' => $this->getLogins(),
            'lastLogin' => $this->getLastLogin()->format(DATETIME::ATOM),
            'loginBeforeLast' =>
                $this->getLoginBeforeLast()->format(DATETIME::ATOM),
            'notes' => $this->getNotes(),
            'accountingFor' => $this->getAccountingFor(),
            'isRoot' => $this->isRoot(),
            'pswdCreated' => $this->getPswdCreated()->format(DATETIME::ATOM)
        );
    }


    /**
     * Extract data for datastore
     *
     * @param int $context
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
        $values = parent::extract($context, $using) + array(
            'uname' => $this->getName(),
            'password' => $this->getPasswordHash(),
            'tPswdCreated' => $this->getPswdCreated()->getTimestamp(),
            'fullname' => $this->getFullName(),
            'groups' => implode(',', $this->getGroups()),
            'tLogin' => $this->getLastLogin()->getTimestamp(),
            'tLastLogin' => $this->getLoginBeforeLast()->getTimestamp(),
            'accountingFor' => $this->getAccountingFor()
        );
        if (isset($this->_status)) {
            $values['status'] = $this->_status;
        }
        if (isset($this->_logins)) {
            $values['nLogins'] = $this->_logins;
        }
        if (isset($this->_invalidAuths)) {
            $values['nInvalidAuths'] = $this->_invalidAuths;
        }
        
        return $values;
    }


    /**
     * Load data from request
     *
     * @param Request $req
     *
     * @return void
     */
    public function loadRequest(Request $req)
    {
        $data = array();

        $data['uname'] = $req->body->get('uname', FILTER_SANITIZE_STRING);

        if ($req->body->is('fullname')) {
            $data['fullname']
                = $req->body->get('fullname', FILTER_SANITIZE_STRING);
        }
        if ($req->body->is('groups')) {
            $data['groups'] = $req->body->get('groups', FILTER_SANITIZE_STRING);
        }
        if ($req->body->is('notes')) {
            $data['notes'] = $req->body->get('notes', FILTER_SANITIZE_STRING);
        }
        if ($req->body->is('accountingFor')) {
            $accountingFor
                = $req->body->get('accountingFor', FILTER_SANITIZE_NUMBER_INT);
            if ($accountingFor != '') {
                $data['accountingFor'] = $accountingFor;
            }
        }
        if ($req->body->is('status')) {
            $status = $req->body->get('status', FILTER_SANITIZE_STRING);
            if ($status == 'activate') {
                $data['status'] = self::STATUS_VALID;
            } elseif ($status == 'block') {
                $data['status'] = self::STATUS_DEACTIVATED;
            }
        }

        if ($req->body->is('newPswdA')) {
            $newPswdA = $req->body->get('newPswdA', FILTER_SANITIZE_STRING);
            $newPswdB = $req->body->get('newPswdB', FILTER_SANITIZE_STRING);

            if (!empty($newPswdA)) {
                if ($newPswdA === $newPswdB) {
                    $this->_password->setPassword($newPswdA);
                    $this->setPswdCreated(time());
                } else {
                    throw new Exception('Lösenorden matchar inte');
                }
            }
        }

        // @codeCoverageIgnoreStart
        if ($req->body->is('genPswd')) {
            $newPswd = $this->_password->generate();
            // TODO Send generated password to user.. (Ticket 1673)
        }
        // @codeCoverageIgnoreEnd

        $this->load($data);
    }


    /**
     * Set user uname
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        assert('is_string($name)');
        $this->_uname = $name;
    }


    /**
     * Get user uname
     *
     * @return string
     */
    public function getName()
    {
        return $this->_uname;
    }


    /**
     * Set full name of user
     *
     * @param string $fullname
     *
     * @return void
     */
    public function setFullName($fullname)
    {
        assert('is_string($fullname)');
        $this->_fullname = $fullname;
    }


    /**
     * Get full name of user
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->_fullname;
    }


    /**
     * Set password hash
     *
     * @param string $hash
     *
     * @return void
     */
    public function setPasswordHash($hash)
    {
        assert('is_string($hash)');
        $this->_password->setHash($hash);
    }


    /**
     * Get password of user
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->_password->getHash();
    }


    /**
     * Check password is valid
     *
     * @param string $password Cleartext password
     *
     * @return bool
     */
    public function isValidPassword($password)
    {
        assert('is_string($password)');

        return $this->_password->isMatch($password);
    }


    /**
     * Get datetime when password was created
     *
     * @return DateTime
     */
    public function getPswdCreated()
    {
        if (!isset($this->_passwordCreated)) {

            return new NullDate();
        }
        
        return $this->_passwordCreated;
    }
    
    
    /**
     * Set timestamp when password was created
     *
     * @param string $pswdCreated
     *
     * @return void
     */
    public function setPswdCreated($pswdCreated)
    {
        $this->_passwordCreated = new DateTime('@' . $pswdCreated);
        $this->_passwordCreated->setTimezone($this->getTimezone());
    }


    /**
     * Set user groups
     *
     * @param array $groups
     *
     * @return void
     */
    public function setGroups(array $groups)
    {
        $this->_groups = $groups;
    }


    /**
     * Get user groups
     *
     * @return array
     */
    public function getGroups()
    {
        if (!isset($this->_groups)) {
 
            return array();
        }
        
        return $this->_groups;
    }
    
    
    /**
     * Check if this user is root
     *
     * @return bool
     */
    public function isRoot()
    {
        return (
            $this->getName() === 'root'
            || in_array('root', $this->getGroups())
        );
    }
    

    /**
     * Set user status
     *
     * Use class constants
     *
     * @param int $status
     *
     * @return void
     */
    public function setStatus($status)
    {
        assert('is_int($status)');
        $this->_status = $status;
    }


    /**
     * Get user status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }


    /**
     * Check if user as active (not blocked)
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getStatus() == self::STATUS_VALID;
    }


    /**
     * Get string describing status for this user
     *
     * @return string Empty string if status is ok
     */
    public function getStatusDesc()
    {
        $descs = array(
            0 => '',
            1 => 'Kontot har spärrats på grund av inaktivitet. Kontakta
                  systemadministratören för att återaktivera kontot och få ett
                  nytt lösenord.',
            2 => 'Kontot har spärrats på grund av upprepade felaktiga
                  inloggningsförsök',
            3 => 'Kontot har spärrats. Kontakta systemadministratören för att
                  återaktivera kontot och få ett nytt lösenord.',
            4 => 'Kontot har spärrats på grund av att lösenordet är för gammalt'
        );

        return $descs[$this->getStatus()];
    }


    /**
     * Set number of user invalid auths
     *
     * @param int $invalidAuths
     *
     * @return void
     */
    public function setInvalidAuths($invalidAuths)
    {
        assert('is_int($invalidAuths)');
        $this->_invalidAuths = $invalidAuths;
    }


    /**
     * Get number of user invalid auths
     *
     * @return int
     */
    public function getInvalidAuths()
    {
        return $this->_invalidAuths;
    }


    /**
     * Increment invalid auths by one
     *
     * @return void
     */
    public function incrementInvalidAuths()
    {
        $nr = intval($this->getInvalidAuths());
        $nr++;
        $this->setInvalidAuths($nr);
    }


    /**
     * Set number of user logins
     *
     * @param int $logins
     *
     * @return void
     */
    public function setLogins($logins)
    {
        assert('is_int($logins)');
        $this->_logins = $logins;
    }


    /**
     * Get number of user logins
     *
     * @return int
     */
    public function getLogins()
    {
        return $this->_logins;
    }


    /**
     * Increment number of logins by one
     *
     * @return void
     */
    public function incrementLogins()
    {
        $nr = intval($this->getLogins());
        $nr++;
        $this->setLogins($nr);
    }


    /**
     * Set timestamp of last login
     *
     * @param string $timestamp
     *
     * @return void
     */
    public function setLastLogin($timestamp)
    {
        $this->_lastLogin = new DateTime('@' . $timestamp);
        $this->_lastLogin->setTimezone($this->getTimezone());
    }


    /**
     * Get datetime for last login
     *
     * @return DateTime
     */
    public function getLastLogin()
    {
        if (!isset($this->_lastLogin)) {
 
            return new NullDate();
        }
        
        return $this->_lastLogin;
    }


    /**
     * Set timestamp of login befor last
     *
     * @param string $timestamp
     *
     * @return void
     */
    public function setLoginBeforeLast($timestamp)
    {
        $this->_loginBeforeLast = new DateTime('@' . $timestamp);
        $this->_loginBeforeLast->setTimezone($this->getTimezone());
    }


    /**
     * Get datetime for login before last
     *
     * @return DateTime
     */
    public function getLoginBeforeLast()
    {
        if (!isset($this->_loginBeforeLast)) {
 
            return new NullDate();
        }
        
        return $this->_loginBeforeLast;
    }


    /**
     * Rotate last-login and login-before-last
     * 
     * Set login-before-last to the current value of last-login. Set last-login
     * to current date.
     *
     * @return void
     */
    public function rotateLoginDates()
    {
        $last = $this->getLastLogin();
        
        if ($last instanceof NullDate) {
            $this->setLoginBeforeLast(time());
        } else {
            $this->setLoginBeforeLast($last->getTimestamp());
        }

        $this->setLastLogin(time());
    }


    /**
     * Set id of faction this user is accounting for
     *
     * @param string $factionId
     *
     * @return void
     */
    public function setAccountingFor($factionId)
    {
        assert('is_string($factionId)');
        return $this->_accountingFor = $factionId;
    }


    /**
     * Get id of faction this user is accounting for
     *
     * @return string
     */
    public function getAccountingFor()
    {
        return $this->_accountingFor;
    }


    /**
     * Get title for this entity
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getName();
    }


    /**
     * Get type descriptor
     *
     * @return string
     */
    public function getType()
    {
        return 'type_user';
    }


    /**
     * Get array describing addresse
     *
     * @return array
     */
    public function getAddressee()
    {
        return array(
            'surname' => $this->getFullName()
        );
    }

}