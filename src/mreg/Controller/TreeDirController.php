<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use Aura\Router\Map;
use Aura\Router\Route;
use itbz\httpio\Response;
use itbz\httpio\Request;
use mreg\Mapper\DirMapper;
use mreg\Tree\NodeTree;
use mreg\Tree\TreeFactoryInterface;
use itbz\Cache\CacheInterface;
use mreg\Model\Dir\DirModel;
use mreg\Dispatch;


/**
 * Base controller for mreg native tree entities
 *
 * Adds treepath information to the entity meta block when reading main
 * resources. Clears tree cache on main create/update/delete
 *
 * @package mreg\Controller
 */
class TreeDirController extends DirController
{

    /**
     * Factory for creating tree
     *
     * @var TreeFactoryInterface
     */
    private $_treefactory;


    /**
     * Cache object
     *
     * @var CacheInterface
     */
    private $_cache;


    /**
     * Tree cache key
     *
     * @var string
     */
    private $_cacheKey;


    /**
     * Base controller for mreg native tree entities
     *
     * @param DirMapper $mapper
     *
     * @param TreeFactoryInterface $treefactory
     *
     * @param CacheInterface $cache
     *
     * @param string $cacheKey
     */
    public function __construct(
        DirMapper $mapper,
        TreeFactoryInterface $treefactory,
        CacheInterface $cache,
        $cacheKey
    )
    {
        parent::__construct($mapper);
        $this->_treefactory = $treefactory;
        $this->_cache = $cache;
        $this->_cacheKey = $cacheKey;
    }


    /**
     * Clear tree cache before updating
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainUpdate(Dispatch $dispatch)
    {
        $this->clearTreeCache();
        return parent::mainUpdate($dispatch);
    }


    /**
     * Clear tree cache before deleting
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainDelete(Dispatch $dispatch)
    {
        $this->clearTreeCache();
        return parent::mainDelete($dispatch);
    }


    /**
     * Clear tree cache before creating
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainCreate(Dispatch $dispatch)
    {
        $this->clearTreeCache();
        return parent::mainCreate($dispatch);
    }


    /**
     * Create view for main entity
     *
     * @param DirModel $entity
     *
     * @param Dispatch $dispatch
     *
     * @return JsonView
     */
    protected function getMainView(DirModel $entity, Dispatch $dispatch)
    {
        $map = $dispatch->getMap();
        $path = $this->getNodeTree($map)->getPath($entity->getId());
        $view = parent::getMainView($entity, $dispatch);
        $view->append('treepath', $path);
        $parent = array_shift($path);
        if ($parent) {
            $view->setLink('up', $parent['link']);
        }
        
        return $view;
    }


    /**
     * Get tree structure
     *
     * Reads from cache if available, else creates a new tree and writes to
     * cache
     *
     * @param Map $map
     *
     * @return NodeTree
     */
    private function getNodeTree(Map $map)
    {
        if ($this->_cache->has($this->_cacheKey)) {
            return new NodeTree($this->_cache->get($this->_cacheKey));
        } else {
            $treedata = $this->_treefactory->createTree($this->_mapper, $map);
            $this->_cache->set($this->_cacheKey, $treedata);
            
            return new NodeTree($treedata);
        }
    }


    /**
     * Removes node tree from cache
     *
     * Triggers the creation of a new tree on next read
     *
     * @return void
     */
    private function clearTreeCache()
    {
        $this->_cache->remove($this->_cacheKey);
    }

}
