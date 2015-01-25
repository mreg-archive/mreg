<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Response;
use itbz\Cache\CacheInterface;


/**
 * Controller for working on the server side cache
 *
 * @package mreg\Controller
 */
class CacheController
{

    /**
     * Cacher object
     *
     * @var CacheInterface
     */
    private $_cache;


    /**
     * Rebuild MyISAM fulltext search indexes
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->_cache = $cache;
    }


    /**
     * Controller for clearing the server cache
     *
     * @return Response
     */
    public function clearCache()
    {
        $this->_cache->clear();
        
        return new Response('', 204);
    }

}