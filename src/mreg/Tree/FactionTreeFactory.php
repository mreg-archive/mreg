<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Tree
 */

namespace mreg\Tree;

use mreg\Mapper\DirMapper;
use itbz\datamapper\pdo\Search;
use Aura\Router\Map;

/**
 * Create tree structure for mreg factions
 *
 * @package mreg\Tree
 */
class FactionTreeFactory implements TreeFactoryInterface
{

    /**
     * Create new tree structure
     *
     * @param DirMapper $factionMapper
     * @param Map $routes
     *
     * @return array
     */
    public function createTree(DirMapper $factionMapper, Map $routes)
    {
        $builder = new NodeTreeBuilder();
        $iterator = $factionMapper->findMany(array(), new Search());
        foreach ($iterator as $faction) {
            $builder->addNode(
                intval($faction->getId()),
                intval($faction->getParentId()),
                array(
                    'name' => $faction->getName(),
                    'type' => $faction->getType(),
                    'link' => $routes->generate(
                        'factions.main',
                        array('mainId' => $faction->getId())
                    )
                )
            );
        }
        
        return $builder->getExportedTree();
    }

}