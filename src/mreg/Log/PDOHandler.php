<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Log
 */

namespace mreg\Log;

use Monolog\Logger;
use PDO;
use PDOStatement;


/**
 * PDO monolog handler for the mreg project
 *
 * @package mreg\Log
 */
class PDOHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    
    /**
     * Set to TRUE when init is done
     *
     * @var bool
     */
    private $_initialized = FALSE;


    /**
     * PDO object
     *
     * @var PDO
     */
    private $_pdo;


    /**
     * PDO statement object
     *
     * @var PDOStatement
     */
    private $_statement;


    /**
     * PDO monolog handler for the mreg project
     *
     * @param PDO $pdo
     *
     * @param int $level Logger level for this handler
     *
     * @param bool $bubble
     */
    public function __construct(
        PDO $pdo,
        $level = Logger::INFO,
        $bubble = TRUE
    )
    {
        parent::__construct($level, $bubble);
        $this->_pdo = $pdo;
    }


    /**
     * Log event to database
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(array $record)
    {
        if (!$this->_initialized) {
            $this->initialize();
        }

        $ip = $url = $method = $user = '';
        
        if (isset($record['extra']['ip'])) {
            $ip = $record['extra']['ip'];
        }

        if (isset($record['extra']['url'])) {
            $url = parse_url($record['extra']['url'], PHP_URL_PATH);
        }

        if (isset($record['extra']['http_method'])) {
            $method = $record['extra']['http_method'];
        }

        if (isset($record['extra']['user'])) {
            $user = $record['extra']['user'];
        }

        $this->_statement->execute(
            array(
                'level' => $record['level_name'],
                'message' => $record['message'],
                'user' => $user,
                'ip' => $ip,
                'url' => $url,
                'http_method' => $method
            )
        );
    }


    /**
     * Prepare pdo statement
     *
     * @return void
     */
    private function initialize()
    {
        $this->_statement = $this->_pdo->prepare(
            'INSERT INTO sys__Log (level, message, user, ip, url, http_method)'
            . ' VALUES (:level, :message, :user, :ip, :url, :http_method)'
        );
        $this->_initialized = TRUE;
    }

}