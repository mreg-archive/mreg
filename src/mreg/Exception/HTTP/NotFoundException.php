<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Exception\HTTP
 */

namespace mreg\Exception\HTTP;


/**
 * Exception for HTTP 404 errors
 *
 * @package mreg\Exception\HTTP
 */
class NotFoundException extends HttpException
{

    /**
     * Exception code is forced to 404
     *
     * @param string $msg
     *
     * @param \Exception $e
     */
    public function __construct($msg = '', \Exception $e = NULL)
    {
        parent::__construct($msg, 404, $e);
    }

}