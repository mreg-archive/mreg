<?php
/**
 * This file is part of the Cache package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package Cache
 */

namespace itbz\Cache;

/**
 * APC cacher
 *
 * @package Cache
 */
class ApcCacher implements CacheInterface
{
    /**
     * Assert that the APC extension is loaded
     *
     * @throws Exception if apc extension is not loaded
     */
    public function __construct()
    {
        if (!extension_loaded('apc')) {
            throw new Exception('APC extension not loaded');
        }
    }

    /**
     * Clear cache
     *
     * @return void
     */
    public function clear()
    {
        apc_clear_cache('user');
    }

    /**
     * Check if cache contains $key
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (function_exists('apc_exists')) {
            return apc_exists($key);
        } else {
            apc_fetch($key, $success);
            return $success;
        }
    }

    /**
     * Get a stored variable from cache.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return apc_fetch($key);
    }

    /**
     * Remove $key from cache
     *
     * @param mixed $key
     *
     * @return void
     */
    public function remove($key)
    {
        apc_delete($key);
    }

    /**
     * Set variable to cache
     *
     * @param mixed $key
     * @param mixed $value
     * @param int $ttl Time to live in seconds
     *
     * @return void
     */
    public function set($key, $value, $ttl = 0)
    {
        apc_store($key, $value, $ttl);
    }
}
