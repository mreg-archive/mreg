<?php
namespace mreg\Controller;

use mreg\tests\Economy\AbstractEconomyTestCase;
use mreg\Controller\CreateAccountantController;
use itbz\httpio\Request;
use mreg\Model\Dir\Faction;
use mreg\Economy\Channels;
use mreg\Economy\TableOfDebits;
use mreg\Economy\Accountant;
use mreg\NullObject\AnonymousUser;

class CreateAccountantControllerTest extends AbstractEconomyTestCase
{
    // tests are outdated

    function testVoid(){}

    /**
     * Get controller for testing to create method
     *
     * @param Matcher $expect Phpunit matcher used when expecting
     * mocked method calls
     *
     * @param Channels $channels Channels object to use when creating controller
     * may be used to force an invalid accountant beeing created
     * /
    function getControllerForCreate($expect, $channels = NULL)
    {
        $accountant = $this->getValidAccountant();

        $accountantMapper = $this->getMock(
            '\mreg\Mapper\AccountantMapper',
            array('getModel', 'save', 'getLastInsertId', 'findByPk'),
            array(),
            '',
            FALSE
        );

        $accountantMapper->expects($expect)
                         ->method('getModel')
                         ->will($this->returnValue($accountant));

        $accountantMapper->expects($expect)
                         ->method('getLastInsertId')
                         ->will($this->returnValue('1'));

        $accountantMapper->expects($expect)
                         ->method('findByPk')
                         ->will($this->returnValue($accountant));

        $factionMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findByPk', 'save', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $factionMapper->expects($this->atLeastOnce())
                      ->method('setAuthUser');

        $factionMapper->expects($expect)
                      ->method('findByPk')
                      ->will($this->returnCallback(function($id){
                            // Faction id == 1 has an accountant
                            if ($id == '1') {
                                $faction = new Faction();
                                $faction->setAccountantId(1);
                                return $faction;
                            }
                            // Other faction does NOT
                            return new Faction();
                      }));

        $factionMapper->expects($expect)
                      ->method('save');

        $kml = $accountant->exportTemplates();
        $sie = $accountant->exportAccounts();

        if (!$channels) {
            $channels = $accountant->getChannels();
        }

        return new CreateAccountantController(
            $accountantMapper,
            $factionMapper,
            $kml,
            $sie,
            $channels,
            $accountant->getDebits()
        );
    }


    /**
     * @ expectedException mreg\Exception
     * /
    function testCreateAccountantExistsError()
    {
        $controller = $this->getControllerForCreate($this->any());

        // Faction with id == 1 has an accountent, se mock above
        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $controller->create($dispatch);
    }


    function testCreateAccountant()
    {
        $controller = $this->getControllerForCreate($this->atLeastOnce());

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '2'));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->create($dispatch);
        
        $this->assertEquals('Bokförare skapades', $response->getContent());
    }


    function testCreateInvalidAccountant()
    {
        // Inject empty channels object to yield invalid accountant
        $channels = new Channels();
        $controller = $this->getControllerForCreate($this->atLeastOnce(), $channels);

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '2'));

        $dispatch = $this->getMock(
            '\mreg\Dispatch',
            array('getRoute', 'getUser'),
            array(),
            '',
            FALSE
        );

        $dispatch->expects($this->atLeastOnce())
                 ->method('getRoute')
                 ->will($this->returnValue($route));

        $dispatch->expects($this->atLeastOnce())
                 ->method('getUser')
                 ->will($this->returnValue(new AnonymousUser));

        $response = $controller->create($dispatch);
        
        $this->assertEquals("199 Bokföringskanal 'K' saknas.", $response->getHeader('Warning'));
    }


    /**
     * @ expectedException mreg\Exception
     * /
    function testNoAccountantError()
    {
        $controller = $this->getControllerForCreate($this->any());

        // Only Faction with id == 1 dows NOT have an accountent, se mock above
        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '2'));

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

        $controller->imExTemplates($dispatch);
    }


    function testExportTemplates()
    {
        $controller = $this->getControllerForCreate($this->any());

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

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

        $response = $controller->imExTemplates($dispatch);

        $this->assertEquals('application/x-download', $response->getHeader('Content-Type'));
        $this->assertEquals('attachment; filename=konteringsmallar.kml', $response->getHeader('Content-Disposition'));
    }


    function getControllerForImport()
    {
        $accountant = $this->getValidAccountant();

        $accountantMapper = $this->getMock(
            '\mreg\Mapper\AccountantMapper',
            array('save', 'findByPk'),
            array(),
            '',
            FALSE
        );

        $accountantMapper->expects($this->any())
                         ->method('findByPk')
                         ->will($this->returnValue($accountant));

        $factionMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findByPk', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $factionMapper->expects($this->atLeastOnce())
                      ->method('setAuthUser');

        $faction = new Faction();
        $faction->setAccountantId(1);

        $factionMapper->expects($this->any())
                      ->method('findByPk')
                      ->will($this->returnValue($faction));

        $kml = $accountant->exportTemplates();
        $sie = $accountant->exportAccounts();

        return new AccountantController(
            $accountantMapper,
            $factionMapper,
            $kml,
            $sie,
            $accountant->getChannels(),
            $accountant->getDebits()
        );
    }


    function testImportTemplates()
    {
        $accountant = $this->getValidAccountant();
        $controller = $this->getControllerForImport();

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('getNextUpload'),
            array('', '', 'POST')
        );

        $upload = $this->getMock(
            '\itbz\httpio\Upload',
            array('getContents'),
            array(),
            '',
            FALSE
        );

        $upload->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue($accountant->exportTemplates()));

        $request->expects($this->once())
                ->method('getNextUpload')
                ->will($this->returnValue($upload));

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

        $response = $controller->imExTemplates($dispatch);
        $this->assertEquals('Import klar', $response->getContent());
    }


    function testImportInvalidTemplates()
    {
        $sie = $this->getMock(
            '\itbz\stb\Accounting\Formatter\SIE'
        );

        $accountant = $this->getMock(
            '\mreg\Economy\Accountant',
            array('importTemplates'),
            array($sie)
        );


        $accountantMapper = $this->getMock(
            '\mreg\Mapper\AccountantMapper',
            array('save', 'findByPk'),
            array(),
            '',
            FALSE
        );

        $accountantMapper->expects($this->any())
                         ->method('findByPk')
                         ->will($this->returnValue($accountant));

        $factionMapper = $this->getMock(
            '\mreg\Mapper\DirMapper',
            array('findByPk', 'setAuthUser'),
            array(),
            '',
            FALSE
        );

        $factionMapper->expects($this->atLeastOnce())
                      ->method('setAuthUser');

        $faction = new Faction();
        $faction->setAccountantId(1);

        $factionMapper->expects($this->any())
                      ->method('findByPk')
                      ->will($this->returnValue($faction));

        $controller = new AccountantController(
            $accountantMapper,
            $factionMapper,
            '',
            '',
            new Channels(),
            new TableOfDebits()
        );

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('getNextUpload'),
            array('', '', 'POST')
        );

        $upload = $this->getMock(
            '\itbz\httpio\Upload',
            array('getContents'),
            array(),
            '',
            FALSE
        );

        $upload->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue(''));

        $request->expects($this->once())
                ->method('getNextUpload')
                ->will($this->returnValue($upload));


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

        $response = $controller->imExTemplates($dispatch);
        
        $expected = array(
            "Warning: 199 Bokföringskanal 'K' saknas."
        );
        
        $this->assertEquals($expected, $response->getHeaders());
    }


    function testExportAccounts()
    {
        $controller = $this->getControllerForCreate($this->any());

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

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

        $response = $controller->imExAccounts($dispatch);

        $this->assertEquals('application/x-download', $response->getHeader('Content-Type'));
    }


    function testImportAccounts()
    {
        $accountant = $this->getValidAccountant();
        $controller = $this->getControllerForImport();

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('getNextUpload'),
            array('', '', 'POST')
        );

        $upload = $this->getMock(
            '\itbz\httpio\Upload',
            array('getContents'),
            array(),
            '',
            FALSE
        );

        $upload->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue($accountant->exportAccounts()));

        $request->expects($this->once())
                ->method('getNextUpload')
                ->will($this->returnValue($upload));

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

        $response = $controller->imExAccounts($dispatch);
        $this->assertEquals('Import klar', $response->getContent());
    }


    function testImportAccountsInvalidAccountant()
    {
        $accountant = $this->getValidAccountant();
        $controller = $this->getControllerForImport();

        $route = new \mreg\tests\RouteMock();
        $route->setValues(array('id' => '1'));

        $request = $this->getMock(
            '\itbz\httpio\Request',
            array('getNextUpload'),
            array('', '', 'POST')
        );

        $upload = $this->getMock(
            '\itbz\httpio\Upload',
            array('getContents'),
            array(),
            '',
            FALSE
        );

        $upload->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue(''));

        $request->expects($this->once())
                ->method('getNextUpload')
                ->will($this->returnValue($upload));

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

        $response = $controller->imExAccounts($dispatch);
        $this->assertEquals("199 Konto '1910' i kanal 'K' saknas i kontoplan", $response->getHeader('Warning'));
    }
    */
}
