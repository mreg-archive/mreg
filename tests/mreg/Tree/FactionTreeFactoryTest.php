<?php
namespace mreg\Tree;
use mreg\Model\Dir\Faction;
use Aura\Router\Map;
use Aura\Router\RouteFactory;


class FactionTreeFactoryTest extends \PHPUnit_Framework_TestCase
{

    function testCreateTree()
    {
        $mapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findMany'),
            array(),
            '',
            FALSE
        );

        $factionA = new Faction();
        $factionA->load(array(
            'id' => '1000',
            'parentId' => '1000',
            'name' => 'CENTRAL',
            'type' => 'ANNAN'
        ));
        $factionB = new Faction();
        $factionB->load(array(
            'id' => '1',
            'parentId' => '1000',
            'name' => 'Stockholms LS',
            'type' => 'LS'
        ));
        $factions = array(
            $factionA,
            $factionB
        );
        
        $mapper->expects($this->once())
               ->method('findMany')
               ->will($this->returnValue($factions));
        
        $map = new Map(new RouteFactory());
        $map->add('factions.main', '/factions/{:mainId}');
        
        $factory = new FactionTreeFactory();
        $tree = $factory->createTree($mapper, $map);

        $expected = array(
            1000 => array(1000, array(1), array(
                'name' => 'CENTRAL',
                'type' => 'type_faction_ANNAN',
                'link' => '/factions/1000'
            )),
            1 => array(1000, array(), array(
                'name' => 'Stockholms LS',
                'type' => 'type_faction_LS',
                'link' => '/factions/1'
            ))
        );

        $this->assertEquals($expected, $tree);
    }

}
