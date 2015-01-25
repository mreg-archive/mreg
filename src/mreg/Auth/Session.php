<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Auth
 */

namespace mreg\Auth;

use PDO;
use mreg\Exception;


/**
 * Session wrapper
 *
 * @package mreg\Auth
 */
class Session
{

    /**
     * PDO instance
     *
     * @var PDO
     */
    private $_pdo;


    /**
     * Name of db table
     *
     * @var string
     */
    private $_table;


    /**
     * Flag if session handling is started
     *
     * @var bool
     */
    private $_bStarted = FALSE;


    /**
     * Session id
     *
     * @var string
     */
    private $_id;


    /**
     * Session user name
     *
     * @var string
     */
    private $_user = '';
    

    /**
     * Session error string
     *
     * @var string
     */
    private $_error = '';


    /**
     * Inject pdo instance
     *
     * @param PDO $pdo
     *
     * @param string $table Name of database table
     *
     * @param bool $autoStart If TRUE (default) session will start on object
     * creation
     */
    public function __construct(PDO $pdo, $table, $autoStart = TRUE)
    {
        assert('is_string($table)');
        assert('is_bool($autoStart)');
        $this->_pdo = $pdo;
        $this->_table = $table;
        if ($autoStart) {
            $this->start();
        }
    }


    /**
     * Close session on destruct
     *
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }


    /**
     * Start session handling
     *
     * @return void
     */
    public function start()
    {
        if (!$this->started()) {
            $this->registerHanders();
            $this->_bStarted = session_start();
        }
    }


    /**
     * Close session
     *
     * @return void
     */
    public function close()
    {
        if ($this->started()) {
            session_write_close();
            $this->_bStarted = FALSE;
        }
    }


    /**
     * Check if session is started
     *
     * @return bool
     */
    public function started()
    {
        return $this->_bStarted;
    }


    /**
     * Regenerate session id
     *
     * @return void
     */
    public function regenerate()
    {
        if ($this->started()) {
            session_regenerate_id(TRUE);
        }
    }


    /**
     * Destroy session and remove data
     *
     * @return void     
     */
    public function destroy()
    {
        if ($this->started()) {
            // Destroy session
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            // Destroy session data
            $_SESSION = array();
            session_destroy();
            $this->_bStarted = FALSE;
        }
    }


    /**
     * Set session id
     *
     * @param string $id
     *
     * @return void
     */
    public function setId($id)
    {
        assert('is_string($id)');
        if ($this->_id != $id) {
            $this->_id = $id;
            session_id($id);
        }
    }


    /**
     * Get session id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Set session user name
     *
     * @param string $user
     *
     * @return void
     */
    public function setUser($user)
    {
        assert('is_string($user)');
        $this->_user = $user;
    }


    /**
     * Get session user name
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_user;
    }


    /**
     * Set session error
     *
     * @param string $error
     *
     * @return void
     */
    public function setError($error)
    {
        assert('is_string($error)');
        $this->_error = $error;
    }


    /**
     * Get session error
     *
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }


    /**
     * Check if data is stored in session
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        assert('is_string($key)');
        return isset($_SESSION[$key]);
    }


    /**
     * Write data to session
     *
     * @param string $key
     *
     * @param scalar $value
     *
     * @return void 
     */
    public function set($key, $value)
    {
        assert('is_string($key)');
        assert('is_scalar($value)');
        $_SESSION[$key] = $value;
    }


    /**
     * Read data from session
     *
     * @param string $key
     *
     * @return scalar
     *
     * @throws Exception if data is not set
     */
    public function get($key)
    {
        assert('is_string($key)');
        if (isset($_SESSION[$key])) {
        
            return $_SESSION[$key];
        }
        
        $msg = "No session data for key '$key'";
        throw new Exception($msg);
    }


    /**
     * Get list of current users
     *
     * @return array
     */
    public function getActiveUsers()
    {
        $stmt = $this->_pdo->query(
            "SELECT DISTINCT `user` FROM `{$this->_table}`"
        );
        $users = array();
        while ($user = $stmt->fetchColumn()) {
            $users[] = $user;
        }

        return $users;
    }


    /**
     * Set error for all sessions of user
     *
     * @param string $user
     *
     * @param string $error
     *
     * @return void
     */
    public function setUserError($user, $error)
    {
        assert('is_string($user)');
        assert('is_string($error)');
        $stmt = $this->_pdo->prepare(
            "UPDATE `{$this->_table}` SET `error` = ? WHERE `user` = ?"
        );
        $stmt->bindValue(1, $error, PDO::PARAM_STR);        
        $stmt->bindValue(2, $user, PDO::PARAM_STR);        
        $stmt->execute();
    }


    /**
     * Register session handler
     *
     * @return void
     */
    private function registerHanders()
    {
        session_set_save_handler(
            function(){
            },
            function(){
            },
            array($this, 'readSessionFromDb'),
            array($this, 'writeSessionToDb'),
            array($this, 'deleteSessionFromDb'),
            array($this, 'collectGarbage')
        );
    }


    /**
     * Read session data from db
     *
     * This method should not be called directly. Use start() to unserialize
     * session data.
     *
     * @param string $id
     *
     * @return string Serialized session data
     */
    public function readSessionFromDb($id)
    {
        $this->setId($id);
        $stmt = $this->_pdo->prepare(
            "SELECT * FROM `{$this->_table}` WHERE `id` = ?"
        );
        $stmt->bindValue(1, $id, PDO::PARAM_STR);        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->setUser($row['user']);
            $this->setError($row['error']);
       
            return $row['data'];
        }
        
        return '';
    }


    /**
     * Write session data to db
     *
     * This function should not be called directly, use close() to serialize
     * data before writing
     *
     * @param string $id
     *
     * @param string $data String of session serialized data
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function writeSessionToDb($id, $data)
    {
        $this->setId($id);
        $this->deleteSessionFromDb($id);
        $stmt = $this->_pdo->prepare(
            "INSERT INTO `{$this->_table}` "
            . "(`id`, `updated`, `user`, `error`, `data`) "
            . "VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bindValue(1, $id, PDO::PARAM_STR);        
        $stmt->bindValue(2, time(), PDO::PARAM_INT);        
        $stmt->bindValue(3, $this->getUser(), PDO::PARAM_STR);        
        $stmt->bindValue(4, $this->getError(), PDO::PARAM_STR);        
        $stmt->bindValue(5, $data, PDO::PARAM_STR);        
        
        return $stmt->execute();
    }


    /**
     * Delete session from db
     *
     * @param string $id
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function deleteSessionFromDb($id)
    {
        $stmt = $this->_pdo->prepare(
            "DELETE FROM `{$this->_table}` WHERE `id` = ?"
        );
        $stmt->bindValue(1, $id, PDO::PARAM_STR);        

        return $stmt->execute();
    }

    
    /**
     * Garbage collection
     *
     * @param int $maxLifetime
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function collectGarbage($maxLifetime)
    {
        $stmt = $this->_pdo->prepare(
            "DELETE FROM `{$this->_table}` WHERE `updated` + ? < ?"
        );
        $stmt->bindValue(1, $maxLifetime, PDO::PARAM_INT);        
        $stmt->bindValue(2, time(), PDO::PARAM_INT);        

        return $stmt->execute();
    }


    /**
     * Create database table
     *
     * For better performance it is recommended to tweak the tabel definition
     * to your needs. This method primarliy exists to show the required table
     * structure.
     *
     * @return void
     */
    public function createDbTable()
    {
        $this->_pdo->exec(
            "CREATE TABLE `{$this->_table}` (
                id VARCHAR(128),
                updated INT,
                user VARCHAR(50),
                error VARCHAR(200),
                data VARCHAR(500),
                PRIMARY KEY(id)
            )"
        );
    }

}
