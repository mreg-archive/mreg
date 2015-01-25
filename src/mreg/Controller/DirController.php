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
use itbz\datamapper\pdo\Search;
use mreg\Mapper\DirMapper;
use mreg\Mapper\RevisionMapper;
use mreg\Model\Model;
use mreg\Model\Dir\DirModel;
use mreg\Exception;
use mreg\Exception\HTTP\PreconditionFailedException;
use mreg\View\JsonView;
use mreg\View\JsonListView;
use mreg\Dispatch;
use itbz\datamapper\pdo\Expression;

/**
 * Base controller for mreg native entities
 *
 * @package mreg\Controller
 */
class DirController implements \mreg\AssocInterface
{

    /**
     * Min entity mapper
     *
     * @var DirMapper
     */
    protected $_mapper;


    /**
     * Base controller for mreg native entities
     *
     * @param DirMapper $mapper
     */
    public function __construct(DirMapper $mapper)
    {
        $this->_mapper = $mapper;
    }


    /**
     * Controller for creating a new main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainCreate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $map = $dispatch->getMap();
        $request = $dispatch->getRequest();
        $entity = $this->_mapper->getNewModel();
        $entity->loadRequest($request);
        $this->_mapper->save($entity);
        $routeValues = array(
            'mainId' => $this->_mapper->getLastInsertId()
        );
        $url = $map->generate($route->name_prefix."main", $routeValues);
        $response = new Response('', 204);
        $response->setHeader('Content-Location', $url);
        
        return $response;
    }


    /**
     * Controller for updating a main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function mainUpdate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $entity = $this->getMainEntity($dispatch->getRoute()->values['mainId']);
        if (!$dispatch->getRequest()->matchEtag($entity->getEtag())) {
            $msg = "Kan ej uppdatera, du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }
        $entity->loadRequest($dispatch->getRequest());
        $this->_mapper->save($entity);

        return new Response('', 204);
    }


    /**
     * Controller for deleting a main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function mainDelete(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $entity = $this->getMainEntity($dispatch->getRoute()->values['mainId']);
        if (!$dispatch->getRequest()->matchEtag($entity->getEtag())) {
            $msg = "Kan ej radera, du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }
        $this->deleteAllXrefs($entity, $dispatch);
        $this->_mapper->delete($entity);
        
        return new Response('', 204);
    }


    /**
     * Controller for reading a main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainRead(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $map = $dispatch->getMap();
        $entity = $this->getMainEntity($route->values['mainId']);

        return $this->getMainView($entity, $dispatch)
            ->setLink(
                '/mreg/rels/mails',
                $map->generate(
                    $route->name_prefix . 'mails',
                    $route->values
                )
            )
            ->setLink(
                '/mreg/rels/phones',
                $map->generate(
                    $route->name_prefix . 'phones',
                    $route->values
                )
            )
            ->setLink(
                '/mreg/rels/addresses',
                $map->generate(
                    $route->name_prefix . 'addresses',
                    $route->values
                )
            )
            ->append(
                'modifiedBy',
                array(
                    'name' => $entity->getModifiedBy(),
                    'link' => $map->generate(
                        'user',
                        array(
                            'name' => $entity->getModifiedBy()
                        )
                    )
                )
            )
            ->append(
                'owner',
                array(
                    'name' => $entity->getOwner(),
                    'link' => $map->generate(
                        'user',
                        array(
                            'name' => $entity->getOwner()
                        )
                    )
                )
            )
            ->append(
                'group',
                array(
                    'name' => $entity->getGroup(),
                    'link' => $map->generate(
                        'group',
                        array(
                            'name' => $entity->getGroup()
                        )
                    )
                )
            )
            ->append('mode', $entity->getMode())
            ->append(
                'addresses',
                $this->getAssocListView(
                    self::ASSOC_ADDRESS,
                    $route,
                    $map
                )->getData()
            )
            ->append(
                'mails',
                $this->getAssocListView(
                    self::ASSOC_MAIL,
                    $route,
                    $map
                )->getData()
            )
            ->append(
                'phones',
                $this->getAssocListView(
                    self::ASSOC_PHONE,
                    $route,
                    $map
                )->getData()
            )
            ->append('revisions', $this->getRevisions($entity))
            ->setReadable(TRUE)
            ->getResponse();
    }


    /**
     * Read paginated collection of entities
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mainCollectionRead(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $map = $dispatch->getMap();
        $search = $this->getPaginationSearch($dispatch->getRequest());
        $iterator = $this->_mapper->findMany(array(), $search);
        $isNextPage = FALSE;
        $count = 0;
        $view = new JsonListView();
        foreach ($iterator as $entity) {
            $count++;
            if ($count >= $search->getLimit()) {
                $isNextPage = TRUE;
                break;
            }
            $view->setItem(
                $entity,
                $map->generate(
                    $route->name_prefix.'main',
                    array('mainId' => $entity->getId())
                )
            );
        }

        return $view->setLinks(
            $this->getPaginationLinks(
                $search,
                $map->generate(
                    $route->name_prefix.'factions',
                    $route->values
                ),
                $isNextPage
            )
        )->getResponse();
    }


    /**
     * Controller for reading a collection of addresses
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addressCollection(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->getAssocListView(
            self::ASSOC_ADDRESS,
            $dispatch->getRoute(),
            $dispatch->getMap()
        )
        ->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading a collection of mail addresses
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mailCollection(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->getAssocListView(
            self::ASSOC_MAIL,
            $dispatch->getRoute(),
            $dispatch->getMap()
        )
        ->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading a collection of phone numbers
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function phoneCollection(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->getAssocListView(
            self::ASSOC_PHONE,
            $dispatch->getRoute(),
            $dispatch->getMap()
        )
        ->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for creating empty addresses
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addressCreate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->createAssoc(
            self::ASSOC_ADDRESS,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for creating empty mail addresses
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mailCreate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->createAssoc(
            self::ASSOC_MAIL,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for creating empty phone numbers
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function phoneCreate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->createAssoc(
            self::ASSOC_PHONE,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for reading an address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addressRead(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());
        $route = $dispatch->getRoute();
        $view = $this->getAssocView(
            self::ASSOC_ADDRESS,
            $route,
            $dispatch->getMap()
        );
        $view->append(
            'rendered',
            $view->getEntity()->getAddress(
                $this->getMainEntity($route->values['mainId'])->getAddressee()
            )
        );
        
        return $view->setReadable(TRUE)->getResponse();
    }


    /**
     * Controller for reading a mail address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mailRead(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->getAssocView(
            self::ASSOC_MAIL,
            $dispatch->getRoute(),
            $dispatch->getMap()
        )
        ->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for reading a phone number
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function phoneRead(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->getAssocView(
            self::ASSOC_PHONE,
            $dispatch->getRoute(),
            $dispatch->getMap()
        )
        ->setReadable(TRUE)
        ->getResponse();
    }


    /**
     * Controller for updating one address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addressUpdate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->updateAssoc(
            self::ASSOC_ADDRESS,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for updating one mail address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mailUpdate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->updateAssoc(
            self::ASSOC_MAIL,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for updating on phone number
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function phoneUpdate(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->updateAssoc(
            self::ASSOC_PHONE,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for deleting an address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function addressDelete(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->deleteAssoc(
            self::ASSOC_ADDRESS,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for deleting a mail address
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function mailDelete(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->deleteAssoc(
            self::ASSOC_MAIL,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Controller for deleting a phone number
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function phoneDelete(Dispatch $dispatch)
    {
        $this->_mapper->setAuthUser($dispatch->getUser());

        return $this->deleteAssoc(
            self::ASSOC_PHONE,
            $dispatch->getRoute(),
            $dispatch->getRequest()
        );
    }


    /**
     * Fetch entity from id
     *
     * @param string $id
     *
     * @return DirModel
     */
    protected function getMainEntity($id)
    {
        return $this->_mapper->findByPk($id);
    }


    /**
     * Create json view
     *
     * Use this as an output hook when subclassing
     *
     * @param DirModel $entity
     *
     * @param Dispatch $dispatch
     *
     * @return JsonView
     */
    protected function getMainView(DirModel $entity, Dispatch $dispatch)
    {
        return new JsonView($entity);
    }


    /**
     * Create list view with referenced content
     *
     * @param callable $fnFind Callable method to find referenced entities. Will
     * be called With main entity and paginated datamapper search object.
     *
     * @param Expression $state Expression for requested xref states
     *
     * @param string $mainRounteName Name of main route for link generation
     *
     * @param string $xrefRouteName Name of referenced route for link generation
     *
     * @param Route $route
     *
     * @param Map $map
     *
     * @param Request $request
     *
     * @return JsonListView
     */
    protected function getXrefListView(
        $fnFind,
        Expression $state,
        $mainRounteName,
        $xrefRouteName,
        Route $route,
        Map $map,
        Request $request = NULL
    )
    {
        assert('is_callable($fnFind)');
        assert('is_string($mainRounteName)');
        assert('is_string($xrefRouteName)');
        if (!$request) {
            $request = new Request();
        }
        $search = $this->getPaginationSearch($request);
        $iterator = call_user_func(
            $fnFind,
            $this->getMainEntity($route->values['mainId']),
            $search,
            $state
        );
        $isNextPage = FALSE;
        $count = 0;
        $view = new JsonListView();
        foreach ($iterator as $objects) {
            list($xref, $entity) = $objects;
            $count++;
            if ($count >= $search->getLimit()) {
                $isNextPage = TRUE;
                break;
            }
            $view->setItem(
                $entity,
                $map->generate(
                    $mainRounteName,
                    array('mainId' => $entity->getId())
                ),
                array('xref' => $xref->export())
            );
        }
        $view->setLinks(
            $this->getPaginationLinks(
                $search,
                $map->generate($xrefRouteName, $route->values),
                $isNextPage
            )
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
        return 0;
    }


    /**
     * Read pagination settings from request
     *
     * Checks startPage and itemsPerPage query vars
     *
     * @param Request $request
     *
     * @return Search
     */
    protected function getPaginationSearch(Request $request)
    {
        if ($request->query->is('startPage')) {
            $page = $request->query->get("startPage", '/^\d+$/');
        } else {
            $page = 1;
        }
        if ($request->query->is('itemsPerPage')) {
            $itemsPerPage = $request->query->get(
                "itemsPerPage",
                '/^\d+$/'
            );
        } else {
            $itemsPerPage = 300;
        }
        $search = new Search();
        // Set limit to itemsPerPage + 1, so we can know if there is a next page
        $search->setLimit($itemsPerPage + 1);
        $search->setStartIndex($itemsPerPage * ($page - 1));

        return $search;    
    }


    /**
     * Create array of paginatin links
     *
     * @param Search $search
     *
     * @param string $url Base link url
     *
     * @param bool $nextPage Flag if link to next page should be included
     *
     * @return array
     */
    protected function getPaginationLinks(Search $search, $url, $nextPage)
    {
        $format = "$url?startPage=%s&itemsPerPage=%s";
        $page = $search->getStartIndex() / ($search->getLimit()-1) + 1;
        $links = array(
            'self' => sprintf($format, $page, $search->getLimit()-1),
            'first' => sprintf($format, '1', $search->getLimit()-1)
        );
        if ($page > 1) {
            $links['prev'] = sprintf($format, $page - 1, $search->getLimit()-1);
        }
        if ($nextPage) {
            $links['next'] = sprintf($format, $page + 1, $search->getLimit()-1);
        }
        
        return $links;
    }


    /**
     * Create an associated record
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Request $request
     *
     * @return Response
     */
    private function createAssoc($assocType, Route $route, Request $request)
    {
        $assoc = $this->_mapper->getNewAssociated($assocType);
        $assoc->setRefId($route->values['mainId']);
        $assoc->loadRequest($request);
        $this->_mapper->saveAssociated($assoc, $assocType);

        return new Response('', 204);
    }


    /**
     * Get view for associated record
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Map $map
     *
     * @return JsonView
     */
    private function getAssocView($assocType, Route $route, Map $map)
    {
        $assoc = $this->_mapper->findAssociated(
            array(
                'id' => $route->values['auxId'],
                'ref_id' => $route->values['mainId']
            ),
            $assocType
        );
        $view = new JsonView($assoc);
        $routeName = $route->name_prefix . $assocType;

        return $view->setLink(
            'self',
            $map->generate($routeName, $route->values)
        )
        ->setLink(
            'edit',
            $map->generate($routeName, $route->values)
        )
        ->append(
            'modifiedBy',
            array(
                'name' => $assoc->getModifiedBy(),
                'link' => $map->generate(
                    'user',
                    array('name' => $assoc->getModifiedBy())
                )
            )
        )
        ->append(
            'revisions',
            $this->getRevisions($assoc, $assocType)
        );
    }


    /**
     * Update an associated record
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Request $request
     *
     * @return Response
     */
    private function updateAssoc($assocType, Route $route, Request $request)
    {
        $assoc = $this->getAssocIfEtagMatch($assocType, $route, $request);
        $assoc->loadRequest($request);
        $this->_mapper->saveAssociated($assoc, $assocType);

        return new Response('', 204);
    }


    /**
     * Delete an associated record
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws Exception if delete failed
     */
    private function deleteAssoc($assocType, Route $route, Request $request)
    {
        $assoc = $this->getAssocIfEtagMatch($assocType, $route, $request);
        if (!$this->_mapper->deleteAssociated($assoc, $assocType)) {
            $msg = "Misslyckades att radera";
            throw new Exception($msg);
        }

        return new Response('', 204);
    }


    /**
     * Get associated record if request etag matches
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws PreconditionFailedException if etag does not match
     */
    private function getAssocIfEtagMatch(
        $assocType,
        Route $route,
        Request $request
    )
    {
        $assoc = $this->_mapper->findAssociated(
            array(
                'id' => $route->values['auxId'],
                'ref_id' => $route->values['mainId']
            ),
            $assocType
        );
        if (!$request->matchEtag($assoc->getEtag())) {
            $msg = "Misslyckades: du arbetar med en gammal version";
            throw new PreconditionFailedException($msg);
        }

        return $assoc;
    }


    /**
     * Get list view of associated data
     *
     * @param string $assocType AssocInterface constant
     *
     * @param Route $route
     *
     * @param Map $map
     *
     * @return JsonListView
     */
    private function getAssocListView($assocType, Route $route, Map $map)
    {
        $iterator = $this->_mapper->findManyAssociated(
            array('ref_id' => $route->values['mainId']),
            new Search(),
            $assocType
        );
        $routeName = $route->name_prefix . $assocType;
        $routeValues = $route->values;
        $view = new JsonListView();
        foreach ($iterator as $assoc) {
            $routeValues['auxId'] = $assoc->getId();
            $view->setItem($assoc, $map->generate($routeName, $routeValues));
        }

        return $view;
    }


    /**
     * Get array of revisions
     *
     * @param Model $entity
     *
     * @param string $assocType AssocInterface constant. If specified revisions
     * for this association type is fetched
     *
     * @return array
     */
    private function getRevisions(Model $entity, $assocType = '')
    {
        $typeTotable = array(
            self::ASSOC_PHONE => 'aux__Phone',
            self::ASSOC_MAIL => 'aux__Mail',
            self::ASSOC_ADDRESS => 'aux__Address',
            '' => ''
        );

        return $this->_mapper->findRevisions(
            $entity->getId(), '', $typeTotable[$assocType]
        );
    }

}