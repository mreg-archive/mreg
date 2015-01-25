<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Tree
 */

namespace mreg\Tree;

use mreg\Mapper\DirMapper;
use Aura\Router\Map;


/**
 * Create tree structure for mreg tree entities
 *
 * @package mreg\Tree
 */
interface TreeFactoryInterface
{

    /**
     * Create new tree structure
     *
     * @param DirMapper $factionMapper
     * @param Map $routes
     *
     * @return array
     */
    public function createTree(DirMapper $factionMapper, Map $routes);

}