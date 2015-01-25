<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Exception\HTTP
 */

namespace mreg\Exception\HTTP;


/**
 * Base Exception for HTTP 400 and 500 errors
 *
 * When the application throws an HttpException the server response will include
 * the implied status code.
 *
 * @package mreg\Exception\HTTP
 */
abstract class HttpException extends \mreg\Exception
{
}