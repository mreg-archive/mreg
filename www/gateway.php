<?php
/**
 * Mreg gateway
 *
 * All http requests are directed to this script.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

use itbz\httpio\Request;
use itbz\httpio\Response;


// Send Retry-After header if $_SERVER['RETRY_AFTER'] is set
if (isset($_SERVER['RETRY_AFTER'])) {
    $datetime = new \DateTime($_SERVER['RETRY_AFTER']);
    header('Retry-After: '.$datetime->format(\DateTime::RFC822), true, 503);
    exit(0);
}


try {
    $pimple = require "bootstrap.php";

    $app = new Application($pimple);
    $request = Request::createFromGlobals();

    $response = $app->dispatch($request, $_SERVER);
    $response->send();

    exit(0);

} catch (\Exception $e) {
    if (isset($pimple)) {
        $pimple['logger']->addAlert((string)$e);
    }
    $response = new Response('Internal Server Error', 500);
    $response->send();
    exit(1);
}