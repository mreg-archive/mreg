<?php
/**
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package utils
 */

namespace itbz\utils;

use itbz\utils\Exception\SerializeException;

/**
 * Serialize and unserialize wrapped objects
 *
 * @package utils
 */
class Serializer
{
    /**
     * Serialize and base64 encode obj
     *
     * @param mixed $obj
     *
     * @return string
     */
    public static function serialize($obj)
    {
        return base64_encode(serialize($obj));
    }

    /**
     * Base64 decode and unserialize $string
     *
     * @param string $string
     *
     * @return mixed
     *
     * @throws SerializeException if unserialize failes
     * @throws SerializeException if __PHP_Incomplete_Class object was returned
     */
    public static function unserialize($string)
    {
        $obj = @unserialize(base64_decode($string));

        if ($obj === false) {
            $err = error_get_last();
            throw new SerializeException($err['message']);
        }

        if (is_a($obj, '__PHP_Incomplete_Class')) {
            // Try to capture name of missing class
            $classname = "";
            if (preg_match(
                "/__PHP_Incomplete_Class_Name'\s*=>\s*'([^']*)/",
                var_export($obj, true),
                $matches
            )) {
                $classname = $matches[1];
            }
            $msg = "Unable to unserialize, class '$classname' is missing";
            throw new SerializeException($msg);
        }

        return $obj;
    }
}
