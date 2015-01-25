<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Exception\HTTP
 */

namespace mreg\Exception\HTTP;


/**
 * Exception for HTTP 412 errors
 *
 * @package mreg\Exception\HTTP
 */
class PreconditionFailedException extends HttpException
{

    /**
     * Exception code is forced to 412
     *
     * @param string $msg
     *
     * @param \Exception $e
     */
    public function __construct($msg = '', \Exception $e = NULL)
    {
        parent::__construct($msg, 412, $e);
    }

}