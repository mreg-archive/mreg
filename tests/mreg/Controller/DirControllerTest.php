<?php
namespace mreg\Controller;

use itbz\httpio\Request;
use itbz\datamapper\pdo\Search;
use mreg\Model\Aux\Address;
use mreg\Model\Dir\Faction;
use itbz\datamapper\exception\DataNotFoundException;
use mreg\Mapper\DirMapper;
use mreg\NullObject\AnonymousUser;

class DirControllerTest extends \PHPUnit_Framework_TestCase
{

    function testMainRead()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findByPk',
                'findManyAssociated',
                'setAuthUser',
                'findRevisions'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $entity = $this->getMock(
            '\mreg\Model\Dir\DirModel',
            array(
                'getType',
                'getTitle',
                'getEtag',
                'export',
                'getAddressee',
                'loadRequest',
                'load',
                'extract',
                'getModifiedBy',
                'getOwner',
                'getGroup'
            )
        );

        $entity->expects($this->atLeastOnce())
               ->method('getModifiedBy')
               ->will($this->returnValue(''));

        $entity->expects($this->atLeastOnce())
               ->method('getOwner')
               ->will($this->returnValue(''));

        $entity->expects($this->atLeastOnce())
               ->method('getGroup')
               ->will($this->returnValue(''));

        $entity->expects($this->atLeastOnce())
               ->method('getEtag')
               ->will($this->returnValue(''));

        $entity->expects($this->once())
               ->method('export')
               ->will($this->returnValue(
                    array(
                        'id' => '1',
                        'name' => 'testname',
                        'etag' => 'testetag',
                        'type' => 'testtype',
                        'title' => 'testtitle',
                    )
               ));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findByPk')
                     ->with('1')
                     ->will($this->returnValue($entity));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findManyAssociated')
                     ->will($this->returnValue(array()));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findRevisions')
                     ->will($this->returnValue(array()));

        $cntrl = new DirController($entityMapper);

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => ''
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );
        
        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));
        
        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mainRead($dispatch);
        
        $content = json_decode($response->getContent());

        $this->assertEquals('1', $content->id);
        $this->assertEquals('testname', $content->name);
        $this->assertEquals('testtype', $content->type);
        $this->assertEquals('testtitle', $content->title);
        $this->assertTrue(isset($content->addresses));
        $this->assertTrue(isset($content->mails));
        $this->assertTrue(isset($content->phones));
        $this->assertTrue(isset($content->revisions));
    }

    
    function testMainCollectionRead()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findMany', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $faction = new Faction;
        $faction->setEtag('');

        $entityMapper->expects($this->once())
                     ->method('findMany')
                     ->will($this->returnValue(array(
                        $faction,
                        $faction
                     )));

        $route = new \mreg\tests\RouteMock();

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $request = new Request('', '', 'GET', array(), array(), array(
            'startPage' => '2',
            'itemsPerPage' => '1'
        ));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mainCollectionRead($dispatch);
        
        $expectedHeaders = array(
            'Content-Type: application/json',
            'Link: ?startPage=2&itemsPerPage=1;rel=self;type="application/json"',
            'Link: ?startPage=1&itemsPerPage=1;rel=first;type="application/json"',
            'Link: ?startPage=1&itemsPerPage=1;rel=prev;type="application/json"',
            'Link: ?startPage=3&itemsPerPage=1;rel=next;type="application/json"'
        );

        $this->assertEquals($expectedHeaders, $response->getHeaders());

        $content = json_decode($response->getContent());
        $this->assertTrue(isset($content->links));
        $this->assertTrue(isset($content->items));
    }


    function testMainCollectionReadStandardPagination()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findMany', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $entityMapper->expects($this->once())
                     ->method('findMany')
                     ->will($this->returnValue(array()));

        $route = new \mreg\tests\RouteMock();

        $map = $this->getMock(
            '\Aura\Router\Map',
            array(),
            array(),
            '',
            FALSE
        );

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mainCollectionRead($dispatch);
        
        $expectedHeaders = array(
            'Content-Type: application/json',
            'Link: ?startPage=1&itemsPerPage=300;rel=self;type="application/json"',
            'Link: ?startPage=1&itemsPerPage=300;rel=first;type="application/json"',
        );

        $this->assertEquals($expectedHeaders, $response->getHeaders());
    }


    function testAddressCollection()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findManyAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $entity = $this->getMock(
            '\mreg\Model\Dir\DirModel',
            array(
                'getType',
                'getTitle',
                'getEtag',
                'export',
                'getAddressee',
                'loadRequest',
                'load',
                'extract',
            )
        );

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array('getEtag', 'export'),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $aux->expects($this->atLeastOnce())
            ->method('export')
            ->will($this->returnValue(array()));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findManyAssociated')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressCollection($dispatch);
    }


    function testMailCollection()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findManyAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Mail',
            array('getEtag', 'isValid'),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findManyAssociated')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mailCollection($dispatch);
    }


    function testPhoneCollection()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findManyAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array(
                'getEtag',
                'getNr',
                'isValid',
                'isMobile',
                'getCountry',
                'getArea'
            ),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findManyAssociated')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneCollection($dispatch);
    }


    function testAddressCreate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('getNewAssociated', 'saveAssociated', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array(),
            array(),
            '',
            FALSE,
            FALSE
        );

        $entityMapper->expects($this->once())
                     ->method('getNewAssociated')
                     ->with(DirController::ASSOC_ADDRESS)
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->once())
                     ->method('saveAssociated')
                     ->with($aux, DirController::ASSOC_ADDRESS)
                     ->will($this->returnValue('1'));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressCreate($dispatch);
        $this->assertEquals(204, $response->getStatus());
    }


    function testMailCreate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('getNewAssociated', 'saveAssociated', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Mail',
            array(),
            array(),
            '',
            FALSE,
            FALSE
        );

        $entityMapper->expects($this->once())
                     ->method('getNewAssociated')
                     ->with(DirController::ASSOC_MAIL)
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->once())
                     ->method('saveAssociated')
                     ->with($aux, DirController::ASSOC_MAIL)
                     ->will($this->returnValue('1'));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mailCreate($dispatch);
        $this->assertEquals(204, $response->getStatus());
    }


    function testPhoneCreate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('getNewAssociated', 'saveAssociated', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array(),
            array(),
            '',
            FALSE,
            FALSE
        );

        $entityMapper->expects($this->once())
                     ->method('getNewAssociated')
                     ->with(DirController::ASSOC_PHONE)
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->once())
                     ->method('saveAssociated')
                     ->with($aux, DirController::ASSOC_PHONE)
                     ->will($this->returnValue('1'));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneCreate($dispatch);
        $this->assertEquals(204, $response->getStatus());
    }


    function testAddressRead()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findByPk',
                'findAssociated',
                'setAuthUser',
                'findRevisions'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $entity = $this->getMock(
            '\mreg\Model\Dir\DirModel',
            array(
                'getType',
                'getTitle',
                'getEtag',
                'export',
                'getAddressee',
                'loadRequest',
                'load',
                'extract',
            )
        );

        $entity->expects($this->once())
               ->method('getAddressee')
               ->will($this->returnValue(array()));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findByPk')
                     ->with('1')
                     ->will($this->returnValue($entity));

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array('export', 'getAddress', 'getEtag'),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('export')
            ->will($this->returnValue(array(
                'meta' => array(
                    'links' => array()
                )
            )));

        $aux->expects($this->atLeastOnce())
            ->method('getAddress')
            ->will($this->returnValue(''));

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findRevisions')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressRead($dispatch);
    }


    function testMailRead()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findByPk',
                'findAssociated',
                'setAuthUser',
                'findRevisions'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Mail',
            array('export', 'getEtag'),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('export')
            ->will($this->returnValue(array(
                'meta' => array(
                    'links' => array()
                )
            )));

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findRevisions')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mailRead($dispatch);
    }


    function testPhoneRead()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findByPk',
                'findAssociated',
                'setAuthUser',
                'findRevisions'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array('export', 'getEtag'),
            array(),
            '',
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('export')
            ->will($this->returnValue(array(
                'meta' => array(
                    'links' => array()
                )
            )));

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findRevisions')
                     ->will($this->returnValue(array($aux)));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $map = $this->getMock(
            '\Aura\Router\Map',
            array('generate'),
            array(),
            '',
            FALSE
        );

        $map->expects($this->atLeastOnce())
            ->method('generate')
            ->will($this->returnValue(''));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getMap', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getMap')
                 ->will($this->returnValue($map));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneRead($dispatch);
    }


    function testAddressUpdate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'saveAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array('getEtag', 'loadRequest'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);
        
        $request = new Request('', '', 'PUT', array(), array(), array(), array(
            'name' => 'testname'
        ));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressUpdate($dispatch);
    }    


    public function testMailUpdate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'saveAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Mail',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);
        
        $request = new Request('', '', 'PUT', array(), array(), array(), array(
            'name' => 'testname'
        ));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mailUpdate($dispatch);
    }


    public function testPhoneUpdate()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'saveAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);
        
        $request = new Request('', '', 'PUT', array(), array(), array(), array(
            'name' => 'testname'
        ));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue($request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneUpdate($dispatch);
    }


    /**
     * @expectedException mreg\Exception\HTTP\PreconditionFailedException
     */
    function testUpdateEtagMissmatchError()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'saveAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue('etag'));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneUpdate($dispatch);
    }


    function testAddressDelete()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'deleteAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('deleteAssociated')
                     ->will($this->returnValue(1));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressDelete($dispatch);
    }

    
    /**
     * @expectedException mreg\Exception
     */
    function testDeleteError()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'deleteAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Address',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('deleteAssociated')
                     ->will($this->returnValue(0));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->addressDelete($dispatch);
    }


    function testMailDelete()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'deleteAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Mail',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('deleteAssociated')
                     ->will($this->returnValue(1));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->mailDelete($dispatch);
    }

    
    function testPhoneDelete()
    {
        $entityMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array(
                'findAssociated',
                'deleteAssociated',
                'setAuthUser'
            ),
            array(),
            '',
            FALSE
        );

        $entityMapper->expects($this->atLeastOnce())
                     ->method('setAuthUser');

        $aux = $this->getMock(
            '\mreg\Model\Aux\Phone',
            array('getEtag'),
            array(),
            '',
            FALSE,
            FALSE
        );

        $aux->expects($this->atLeastOnce())
            ->method('getEtag')
            ->will($this->returnValue(''));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('findAssociated')
                     ->will($this->returnValue($aux));

        $entityMapper->expects($this->atLeastOnce())
                     ->method('deleteAssociated')
                     ->will($this->returnValue(1));

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array(
            'mainId' => '1',
            'auxId' => '1'
        ));

        $cntrl = new DirController($entityMapper);

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getRequest', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRequest')
                 ->will($this->returnValue(new Request));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $cntrl->phoneDelete($dispatch);
    }
}
