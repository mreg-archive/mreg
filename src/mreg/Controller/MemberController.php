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
use itbz\datamapper\exception\DataNotFoundException;
use mreg\Exception\HTTP\ConflictException;
use itbz\stb\ID\FakeId;
use itbz\datamapper\pdo\Expression;
use itbz\datamapper\pdo\access\AcMapper;

/**
 * Controller for member entities
 *
 * @package mreg\Controller
 */
class MemberController extends DirController
{

    /**
     * Mapper for referenced factions
     *
     * @var XrefMapper
     */
    private $_factionXrefMapper;


    /**
     * Invoice mapper
     *
     * @var AcMapper
     */
    private $_invoiceMapper;


    /**
     * Controller for faction entities
     *
     * @param DirMapper $mapper
     *
     * @param XrefMapper $factionXrefMapper
     *
     * @param AcMapper $invoiceMapper
     */
    public function __construct(
        DirMapper $mapper,
        XrefMapper $factionXrefMapper,
        AcMapper $invoiceMapper
    )
    {
        parent::__construct($mapper);
        $this->_factionXrefMapper = $factionXrefMapper;
        $this->_invoiceMapper = $invoiceMapper;
    }


    /**
     * Validate that personal id does not exist on create
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws ConflictException if personal id is already in use
     */
    public function mainCreate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $req = $dispatch->getRequest();
        // Validate personal id in database
        $strPersonalId = $req->body->get('personalId', FILTER_SANITIZE_STRING);
        $member = $this->_mapper->getNewModel();
        $member->setPersonalId($strPersonalId);
        $personalId = $member->getPersonalId();
        if ($personalId instanceof FakeId) {

                return parent::mainCreate($dispatch);
        }

        try {
            $member = $this->_mapper->find(
                array('personalId' => $personalId)
            );
        } catch(DataNotFoundException $e) {

            return parent::mainCreate($dispatch);
        }
        $msg = "Personnumret finns redan i databasen";
        throw new ConflictException($msg);
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
        $state = $request->body->get('state', FILTER_SANITIZE_STRING);
        if ($request->body->is('comment')) {
            $comment = $request->body->get('comment', FILTER_SANITIZE_STRING);
        } else {
            $comment = '';
        }
        $this->_factionXrefMapper->deactivate(
            $route->values['xrefId'],
            $route->values['mainId'],
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
        $route = $dispatch->getRoute();
        $map = $dispatch->getMap();
        $user = $dispatch->getUser();
        $this->_factionXrefMapper->setAuthUser($user);
        $this->_invoiceMapper->setUser($user->getName(), $user->getGroups());
        $view = parent::getMainView($entity, $dispatch);
        $view->setLink('self', $map->generate('members.main', $route->values))
             ->setLink('edit', $map->generate('members.main', $route->values))
             ->setLink(
                 '/mreg/rels/factions',
                 $map->generate('members.factions', $route->values)
             )
             ->setLink(
                 '/mreg/rels/history',
                 $map->generate('members.history', $route->values)
             )
             ->append(
                 'invoices',
                 $this->getUnpaidInvoices($route->values['mainId'], $map)
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

        return $this->_factionXrefMapper->unlinkAll($entity->getId());
    }


    /**
     * Get unpaid incoices for member id
     *
     * @param string $id
     *
     * @param Map $map
     *
     * @return array
     */
    private function getUnpaidInvoices($id, Map $map)
    {
        assert('is_string($id)');

        try {
            $iter = $this->_invoiceMapper->findMany(
                array('payerId' => $id, 'paidVia' => ''),
                new Search
            );
        } catch (\itbz\datamapper\pdo\access\AccessDeniedException $e) {
            // Users not allowed to read invoices should still se member
            return array();
        }

        $invoiceView = new JsonListView();
        foreach ($iter as $invoice) {
            $invoiceView->setItem(
                $invoice,
                $map->generate(
                    'member-invoices',
                    array('id' => $invoice->getId())
                )
            );
        }
        
        return $invoiceView->getItems();
    }

}
