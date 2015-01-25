<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Negotiator;
use itbz\httpio\Response;
use itbz\httpio\Request;
use Aura\Router\Map;
use Aura\Router\Route;
use mreg\Dispatch;


/**
 * Client bootstrap and logout controller
 *
 * @package mreg\Controller
 */
class ClientController
{

    /**
     * Bootstrap javascript client
     *
     * If user requests html redirect to client application. If user requests
     * json send bootstrap data to client.
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function bootstrap(Dispatch $dispatch)
    {
        $map = $dispatch->getMap();
        $request = $dispatch->getRequest();
        $negotiator = new Negotiator(
            array(
                'application/json' => 1.0,
                'text/html' => 1.0,
            )
        );

        $ctype = 'text/html';

        if ($request->headers->is('Accept')) {
            $ctype = $negotiator->negotiate(
                $request->headers->get(
                    'Accept',
                    '/^[a-zA-Z\/+,;=.*() 0-9-]+$/'
                )
            );
        }

        $response = new Response();
        $response->setHeader('Vary', 'Accept');

        $rootUrl = $map->generate('jsclient', array());

        if ($ctype != 'application/json') {
            // Redirect to jsclient
            $location = $rootUrl . 'jsclient/index.html';
            $response->setStatus(303);
            $response->setHeader('Location', $location);
        } else {
            // Send jsclient bootstrap data
            $response->setHeader('Content-Type', 'application/json');
            $content = array(
                'serviceUrl' => $rootUrl,
                'links' => array(
                    'logout' => $map->generate('logout'),
                    'upload' => $map->generate('upload'),
                    'clearCache' => $map->generate('clear-cache'),
                    'createAccountant' => $map->generate('create-accountant'),
                    'accountant' => $map->generate('accountant.read'),
                    'templates' => $map->generate('accountant.kml'),
                    'accounts' => $map->generate('accountant.accounts'),
                    'billMembers' => $map->generate('accountant.bill'),
                    'printInvoices' => $map->generate('accountant.print'),
                    'exportInvoices' => $map->generate('accountant.export'),
                ),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'useAuthHeader' => TRUE,
                'requireCookies' => TRUE,
                'user' => $dispatch->getUser()->export(),
            );
            $response->setContent(json_encode($content));
        }

        return $response;
    }


    /**
     * Logout user session
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function logout(Dispatch $dispatch)
    {
        $dispatch->getSession()->destroy();

        return new Response('', 204);
    }

}
