<?php
/**
 * Bootstrap for mreg CLI
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Cli
 */

$c = include __DIR__ . "/../www/bootstrap.php";

$userMapper = $c['userMapper'];
$userMapper->setUser('root');
$c['user'] = $userMapper->findByPk('cli');

ini_set('html_errors', '0');

$logger = $c['logger'];

/**
 * Logg errors
 *
 * @param int $errno
 * @param string $msg
 * @param string $file
 * @param int $line
 *
 * @package mreg\Cli
 */
function cliErrorHandler ($errno, $msg, $file, $line) {
    global $logger;
    $file = basename($file);
    $str = "$msg in $file on line $line";
    $logger->addAlert($str);

    return FALSE;    
}

set_error_handler('cliErrorHandler');

/**
 * Logg exceptions
 *
 * @param Exception $e
 *
 * @package mreg\Cli
 */
set_exception_handler(function($e) use ($logger) {
    $msg = get_class($e) . " :: " . $e->getMessage();
    cliErrorHandler(E_USER_ERROR, $msg, $e->getFile(), $e->getLine());
    throw $e;
});

return $c;
