<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Exception\HTTP
 */

namespace mreg\Exception\HTTP;


/**
 * HTTP 409 conflict exception
 *
 * @package mreg\Exception\HTTP
 */
class ConflictException extends HttpException
{

    /**
     * Exception code is forced to 403
     *
     * @param string $msg
     *
     * @param \Exception $e
     */
    public function __construct($msg = '', \Exception $e = NULL)
    {
        parent::__construct($msg, 409, $e);
    }

}