<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\datamapper\pdo\access\AcMapper;
use itbz\httpio\Response;
use itbz\httpio\Request;
use mreg\Dispatch;
use itbz\datamapper\pdo\Search;
use mreg\Exception\HTTP\PreconditionFailedException;
use mreg\View\JsonView;
use mreg\View\JsonListView;

/**
 * Controller for system models (Users and system groups)
 *
 * @package mreg\Controller
 */
class SystemController
{

    /**
     * Data mapper
     *
     * @var AcMapper
     */
    protected $_mapper;


    /**
     * Controller for system models (Users and system groups)
     *
     * @param AcMapper $mapper
     */
    public function __construct(AcMapper $mapper)
    {
        $this->_mapper = $mapper;
    }


    /**
     * Controller for reading an entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function read(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $route = $dispatch->getRoute();
        $map = $dispatch->getMap();
        $this->_mapper->setUser($user->getName(), $user->getGroups());
        $entity = $this->_mapper->findByPk($route->values['name']);
        $view = new JsonView($entity);

        return $view->setLink('self', $route->generate())
            ->setLink('edit', $route->generate())
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
            ->setReadable(TRUE)
            ->getResponse();
    }


    /**
     * Conroller for reading collection of entities
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function readCollection(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $route = $dispatch->getRoute();
        $this->_mapper->setUser($user->getName(), $user->getGroups());
        $iterator = $this->_mapper->findMany(array(), new Search);
        $view = new JsonListView();
        foreach ($iterator as $entity) {
            $view->setItem(
                $entity,
                $dispatch->getMap()->generate(
                    $route->values['generateUsing'],
                    array('name' => $entity->getName())
                )
            );
        }

        return $view->setReadable(FALSE)
            ->setLinks(
                array('self', $route->generate())
            )
            ->setReadable(TRUE)
            ->getResponse();
    }


    /**
     * Controller for creating a new entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function create(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $this->_mapper->setUser($user->getName(), $user->getGroups());
        $entity = $this->_mapper->getNewModel();
        $entity->loadRequest($dispatch->getRequest());
        $this->_mapper->save($entity);
        $url = $dispatch->getMap()->generate(
            $dispatch->getRoute()->values['generateUsing'],
            array('name' => $entity->getName())
        );
        $response = new Response('', 204);
        $response->setHeader('Content-Location', $url);
        
        return $response;
    }


    /**
     * Controller for updating entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function update(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $this->_mapper->setUser($user->getName(), $user->getGroups());
        $entity = $this->_mapper->findByPk(
            $dispatch->getRoute()->values['name']
        );
        if (!$dispatch->getRequest()->matchEtag($entity->getEtag())) {
            $msg = "Kan ej uppdatera, du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }
        $entity->loadRequest($dispatch->getRequest());
        $entity->setModifiedBy($user->getName());
        $this->_mapper->save($entity);

        return new Response('', 204);
    }


    /**
     * Controller for deleting entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function delete(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $this->_mapper->setUser($user->getName(), $user->getGroups());
        $entity = $this->_mapper->findByPk(
            $dispatch->getRoute()->values['name']
        );
        if (!$dispatch->getRequest()->matchEtag($entity->getEtag())) {
            $msg = "Kan ej radera, du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }
        $this->_mapper->delete($entity);
        
        return new Response('', 204);
    }

}
