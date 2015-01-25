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
use mreg\Tree\TreeFactoryInterface;
use itbz\Cache\CacheInterface;
use mreg\Mapper\XrefMapper;
use itbz\datamapper\pdo\Search;
use mreg\View\JsonListView;
use mreg\Model\Dir\DirModel;
use mreg\Dispatch;
use itbz\datamapper\pdo\Expression;

/**
 * Controller for faction entities
 *
 * @package mreg\Controller
 */
class FactionController extends TreeDirController
{

    /**
     * Mapper for referenced factions
     *
     * @var XrefMapper
     */
    private $_factionXrefMapper;


    /**
     * Mapper for referenced members
     *
     * @var XrefMapper
     */
    private $_memberXrefMapper;


    /**
     * Controller for faction entities
     *
     * @param DirMapper $mapper
     *
     * @param TreeFactoryInterface $treefactory
     *
     * @param CacheInterface $cache
     *
     * @param string $cacheKey
     *
     * @param XrefMapper $factionXrefMapper
     *
     * @param XrefMapper $memberXrefMapper
     */
    public function __construct(
        DirMapper $mapper,
        TreeFactoryInterface $treefactory,
        CacheInterface $cache,
        $cacheKey,
        XrefMapper $factionXrefMapper,
        XrefMapper $memberXrefMapper
    )
    {
        parent::__construct($mapper, $treefactory, $cache, $cacheKey);
        $this->_factionXrefMapper = $factionXrefMapper;
        $this->_memberXrefMapper = $memberXrefMapper;
    }


    /**
     * Controller for reading factions I am a member of
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function getXrefFactions(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());

        return $this->getXrefListView(
            array($this->_factionXrefMapper, 'findXrefMasters'),
            new Expression('state', 'OK'),
            'factions.main',
            'factions.factions',
            $dispatch->getRoute(),
            $dispatch->getMap(),
            $dispatch->getRequest()
        )
        //->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading factions I used to be a member of
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function getXrefHistoricFactions(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());

        return $this->getXrefListView(
            array($this->_factionXrefMapper, 'findXrefMasters'),
            new Expression('state', 'OK', '!='),
            'factions.main',
            'factions.history',
            $dispatch->getRoute(),
            $dispatch->getMap(),
            $dispatch->getRequest()
        )
        //->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading factions that are members of me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function getXrefMemberFactions(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());

        return $this->getXrefListView(
            array($this->_factionXrefMapper, 'findXrefForeigns'),
            new Expression('state', 'OK'),
            'factions.main',
            'factions.member-factions',
            $dispatch->getRoute(),
            $dispatch->getMap(),
            $dispatch->getRequest()
        )
        //->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading members of me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function getXrefMembers(Dispatch $dispatch)
    {
        $this->_memberXrefMapper->setAuthUser($dispatch->getUser());

        return $this->getXrefListView(
            array($this->_memberXrefMapper, 'findXrefForeigns'),
            new Expression('state', 'OK'),
            'members.main',
            'members.members',
            $dispatch->getRoute(),
            $dispatch->getMap(),
            $dispatch->getRequest()
        )
        //->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for making a faction a member of me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addFactionToMe(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $this->_factionXrefMapper->link(
            $dispatch->getRoute()->values['mainId'],
            $dispatch->getRequest()->body->get('id', FILTER_VALIDATE_INT)
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for making me a member of a faction
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addMeToFaction(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $this->_factionXrefMapper->link(
            $dispatch->getRequest()->body->get('id', FILTER_VALIDATE_INT),
            $dispatch->getRoute()->values['mainId']
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for adding a member to me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addMemberToMe(Dispatch $dispatch)
    {
        $this->_memberXrefMapper->setAuthUser($dispatch->getUser());
        $this->_memberXrefMapper->link(
            $dispatch->getRoute()->values['mainId'],
            $dispatch->getRequest()->body->get('id', FILTER_VALIDATE_INT)
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for removing a faction from me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function removeFactionFromMe(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $this->_factionXrefMapper->unlink(
            $route->values['mainId'],
            $route->values['xrefId']
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for removing me from a faction
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function removeMeFromFaction(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $this->_factionXrefMapper->unlink(
            $route->values['xrefId'],
            $route->values['mainId']
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for removing a member from me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function removeMemberFromMe(Dispatch $dispatch)
    {
        $this->_memberXrefMapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $this->_memberXrefMapper->unlink(
            $route->values['mainId'],
            $route->values['xrefId']
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for deactivating faction from me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function deactivateFactionFromMe(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $request = $dispatch->getRequest();
        $route = $dispatch->getRoute();
        if ($request->body->is('comment')) {
            $comment = $request->body->get('comment', FILTER_SANITIZE_STRING);
        } else {
            $comment = '';
        }
        $this->_factionXrefMapper->deactivate(
            $route->values['mainId'],
            $route->values['xrefId'],
            'HISTORIC',
            $comment
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for deactivating me from faction
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function deactivateMeFromFaction(Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $request = $dispatch->getRequest();
        $route = $dispatch->getRoute();
        if ($request->body->is('comment')) {
            $comment = $request->body->get('comment', FILTER_SANITIZE_STRING);
        } else {
            $comment = '';
        }
        $this->_factionXrefMapper->deactivate(
            $route->values['xrefId'],
            $route->values['mainId'],
            'HISTORIC',
            $comment
        );
        
        return new Response('', 204);
    }


    /**
     * Controller for deactivating member from me
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function deactivateMemberFromMe(Dispatch $dispatch)
    {
        $this->_memberXrefMapper->setAuthUser($dispatch->getUser());
        $request = $dispatch->getRequest();
        $route = $dispatch->getRoute();
        $state = $request->body->get('state', FILTER_SANITIZE_STRING);
        if ($request->body->is('comment')) {
            $comment = $request->body->get('comment', FILTER_SANITIZE_STRING);
        } else {
            $comment = '';
        }
        $this->_memberXrefMapper->deactivate(
            $route->values['mainId'],
            $route->values['xrefId'],
            $state,
            $comment
        );
        
        return new Response('', 204);
    }


    /**
     * Add faction links
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
        $route = $dispatch->getRoute();
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $view = parent::getMainView($entity, $dispatch);
        $view->setLink('self', $map->generate('factions.main', $route->values))
             ->setLink('edit', $map->generate('factions.main', $route->values))
             ->setLink(
                 '/mreg/rels/factions',
                 $map->generate('factions.factions', $route->values)
             )
             ->setLink(
                 '/mreg/rels/member-factions',
                 $map->generate('factions.member-factions', $route->values)
             )
             ->setLink(
                 '/mreg/rels/history',
                 $map->generate('factions.history', $route->values)
             )
             ->setLink(
                 '/mreg/rels/members',
                 $map->generate('factions.members', $route->values)
             )
             ->append(
                 'factions',
                 $this->getXrefListView(
                     array($this->_factionXrefMapper, 'findXrefMasters'),
                     new Expression('state', 'OK'),
                     'factions.main',
                     'factions.factions',
                     $route,
                     $map
                 )->getItems()
             );
        
        return $view;
    }


    /**
     * Hook for deleting references before main delete
     *
     * @param DirModel $entity
     *
     * @param Dispatch $dispatch
     *
     * @return int Number of affected rows
     */
    protected function deleteAllXrefs(DirModel $entity, Dispatch $dispatch)
    {
        $this->_factionXrefMapper->setAuthUser($dispatch->getUser());
        $this->_memberXrefMapper->setAuthUser($dispatch->getUser());
        $nRows = $this->_factionXrefMapper->unlinkAll($entity->getId());
        $nRows += $this->_memberXrefMapper->unlinkAll($entity->getId());
        
        return $nRows;
    }

}
