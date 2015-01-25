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
 * XCache cacher
 *
 * @package Cache
 */
class XCacheCacher implements CacheInterface
{
    /**
     * Assert that the APC extension is loaded
     *
     * @throws Exception if xcache extension is not loaded
     */
    public function __construct()
    {
        if (!extension_loaded('xcache')) {
            throw new Exception('XCache extension not loaded');
        }
    }

    /**
     * Clear cache
     *
     * @return void
     */
    public function clear()
    {
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
            xcache_clear_cache(XC_TYPE_VAR, $i);
        }
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
        return xcache_isset($key);
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
        return xcache_get($key);
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
        xcache_unset($key);
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
        xcache_set($key, $value, $ttl);
    }
}
