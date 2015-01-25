<?php
/**
 * Returns a aura router map with all mreg routes registered.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

use Aura\Router\Map;
use Aura\Router\RouteFactory;
use mreg\AssocInterface;


/**
 * Create route map with mreg routes
 *
 * @return Map
 *
 * @uses string $wwwRoot WWW path to application
 * @uses bool $requireHttps TRUE if routing should require HTTPS
 *
 * @package mreg
 */
return call_user_func(function() use ($wwwRoot, $requireHttps)
{

    /**
     * Standard routes used with DirController
     */
    $dirControllerRoutes = array(
        // Main
        'collection' => array(
            'path' => '{:void:()}',
            'method' => array('GET'),
            'values' => array('action' => 'mainCollectionRead')
        ),
        array(
            'path' => '{:void:()}',
            'method' => array('POST'),
            'values' => array('action' => 'mainCreate')
        ),
        'main' => array(
            'path' => '/{:mainId}',
            'method' => array('GET'),
            'values' => array('action' => 'mainRead')
        ),
        array(
            'path' => '/{:mainId}',
            'method' => array('PUT'),
            'values' => array('action' => 'mainUpdate')
        ),
        array(
            'path' => '/{:mainId}',
            'method' => array('DELETE'),
            'values' => array('action' => 'mainDelete')
        ),
        
        // Addresses
        'addresses' => array(
            'path' => '/{:mainId}/addresses',
            'method' => array('GET'),
            'values' => array('action' => 'addressCollection')
        ),
        array(
            'path' => '/{:mainId}/addresses',
            'method' => array('POST'),
            'values' => array('action' => 'addressCreate')
        ),
        AssocInterface::ASSOC_ADDRESS => array(
            'path' => '/{:mainId}/addresses/{:auxId}',
            'method' => array('GET'),
            'values' => array('action' => 'addressRead')
        ),
        array(
            'path' => '/{:mainId}/addresses/{:auxId}',
            'method' => array('PUT'),
            'values' => array('action' => 'addressUpdate')
        ),
        array(
            'path' => '/{:mainId}/addresses/{:auxId}',
            'method' => array('DELETE'),
            'values' => array('action' => 'addressDelete')
        ),

        // Mails
        'mails' => array(
            'path' => '/{:mainId}/mails',
            'method' => array('GET'),
            'values' => array('action' => 'mailCollection')
        ),
        array(
            'path' => '/{:mainId}/mails',
            'method' => array('POST'),
            'values' => array('action' => 'mailCreate')
        ),
        AssocInterface::ASSOC_MAIL => array(
            'path' => '/{:mainId}/mails/{:auxId}',
            'method' => array('GET'),
            'values' => array('action' => 'mailRead')
        ),
        array(
            'path' => '/{:mainId}/mails/{:auxId}',
            'method' => array('PUT'),
            'values' => array('action' => 'mailUpdate')
        ),
        array(
            'path' => '/{:mainId}/mails/{:auxId}',
            'method' => array('DELETE'),
            'values' => array('action' => 'mailDelete')
        ),

        // Phone numbers
        'phones' => array(
            'path' => '/{:mainId}/phones',
            'method' => array('GET'),
            'values' => array('action' => 'phoneCollection')
        ),
        array(
            'path' => '/{:mainId}/phones',
            'method' => array('POST'),
            'values' => array('action' => 'phoneCreate')
        ),
        AssocInterface::ASSOC_PHONE => array(
            'path' => '/{:mainId}/phones/{:auxId}',
            'method' => array('GET'),
            'values' => array('action' => 'phoneRead')
        ),
        array(
            'path' => '/{:mainId}/phones/{:auxId}',
            'method' => array('PUT'),
            'values' => array('action' => 'phoneUpdate')
        ),
        array(
            'path' => '/{:mainId}/phones/{:auxId}',
            'method' => array('DELETE'),
            'values' => array('action' => 'phoneDelete')
        )
    );


    /**
     * The routes...
     */
    $routes = array(


        /**
         * Client bootstrap
         */
        $wwwRoot => array(
            'secure' => $requireHttps,
            'routes' => array(
                'jsclient' => array(
                    'path' => '{:void:()}',
                    'method' => array('GET'),
                    'values' => array(
                        'controller' => 'clientController',
                        'action' => 'bootstrap'
                    )
                )
            )
        ),


        /**
         * Search
         */
        $wwwRoot . 'search' => array(
            'secure' => $requireHttps,
            'routes' => array(
                'search' => array(
                    'path' => '/?',
                    'method' => array('GET'),
                    'values' => array(
                        'controller' => 'searchController',
                        'action' => 'search'
                    )
                )
            )
        ),


        /**
         * Controller actions
         */
        $wwwRoot . 'controllers' => array(
            'secure' => $requireHttps,
            'routes' => array(
                'logout' => array(
                    'path' => '/service-logout',
                    'method' => array('GET'),
                    'values' => array(
                        'controller' => 'clientController',
                        'action' => 'logout'
                    )
                ),
                'clear-cache' => array(
                    'path' => '/clear-cache',
                    'method' => array('POST'),
                    'values' => array(
                        'controller' => 'cacheController',
                        'action' => 'clearCache'
                    )
                ),
                'create-accountant' => array(
                    'path' => '/create-accountant',
                    'method' => array('POST','GET'),
                    'values' => array(
                        'controller' => 'createAccountantController',
                        'action' => 'create'
                    )
                )
            )
        ),


        /**
         * System users
         */
        $wwwRoot . 'users' => array(
            'secure' => $requireHttps,
            'params' => array(
                ':name' => '([a-zA-Z_-]+)',
            ),
            'values' => array(
                'controller' => 'userController',
                'generateUsing' => 'user'
            ),
            'routes' => array(
                array(
                    'path' => '{:void:()}',
                    'method' => array('GET'),
                    'values' => array('action' => 'readCollection')
                ),
                array(
                    'path' => '{:void:()}',
                    'method' => array('POST'),
                    'values' => array('action' => 'create')
                ),
                'user' => array(
                    'path' => '/{:name}',
                    'method' => array('GET'),
                    'values' => array('action' => 'read')
                ),
                array(
                    'path' => '/{:name}',
                    'method' => array('PUT'),
                    'values' => array('action' => 'update')
                ),
                array(
                    'path' => '/{:name}',
                    'method' => array('DELETE'),
                    'values' => array('action' => 'delete')
                )
            )
        ),


        /**
         * System groups
         */
        $wwwRoot . 'sys_groups' => array(
            'secure' => $requireHttps,
            'params' => array(
                ':name' => '([a-zA-Z_-]+)',
            ),
            'values' => array(
                'controller' => 'systemGroupController',
                'generateUsing' => 'group'
            ),
            'routes' => array(
                array(
                    'path' => '{:void:()}',
                    'method' => array('GET'),
                    'values' => array('action' => 'readCollection')
                ),
                array(
                    'path' => '{:void:()}',
                    'method' => array('POST'),
                    'values' => array('action' => 'create')
                ),
                'group' => array(
                    'path' => '/{:name}',
                    'method' => array('GET'),
                    'values' => array('action' => 'read')
                ),
                array(
                    'path' => '/{:name}',
                    'method' => array('PUT'),
                    'values' => array('action' => 'update')
                ),
                array(
                    'path' => '/{:name}',
                    'method' => array('DELETE'),
                    'values' => array('action' => 'delete')
                )
            )
        ),


        /**
         * Factions
         */
        $wwwRoot . 'factions' => array(
            'name_prefix' => 'factions.',
            'secure' => $requireHttps,
            'params' => array(
                'mainId' => '(\d+)',
                'auxId' => '(\d+)',
                'xrefId' => '(\d+)'
            ),
            'values' => array(
                'controller' => 'factionController',
                'auxId' => ''
            ),
            'routes' => array_merge(
                $dirControllerRoutes,
                array(
                    'factions' => array(
                        'path' => '/{:mainId}/factions',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefFactions')
                    ),
                    array(
                        'path' => '/{:mainId}/factions',
                        'method' => array('POST'),
                        'values' => array('action' => 'addMeToFaction')
                    ),
                    array(
                        'path' => '/{:mainId}/factions/{:xrefId}',
                        'method' => array('DELETE'),
                        'values' => array('action' => 'removeMeFromFaction')
                    ),
                    array(
                        'path' => '/{:mainId}/factions/{:xrefId}',
                        'method' => array('PUT'),
                        'values' => array('action' => 'deactivateMeFromFaction')
                    ),
                    'member-factions' => array(
                        'path' => '/{:mainId}/member-factions',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefMemberFactions')
                    ),
                    array(
                        'path' => '/{:mainId}/member-factions',
                        'method' => array('POST'),
                        'values' => array('action' => 'addFactionToMe')
                    ),
                    array(
                        'path' => '/{:mainId}/member-factions/{:xrefId}',
                        'method' => array('DELETE'),
                        'values' => array('action' => 'removeFactionFromMe')
                    ),
                    array(
                        'path' => '/{:mainId}/member-factions/{:xrefId}',
                        'method' => array('PUT'),
                        'values' => array('action' => 'deactivateFactionFromMe')
                    ),
                    'history' => array(
                        'path' => '/{:mainId}/history',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefHistoricFactions')
                    ),

                    'members' => array(
                        'path' => '/{:mainId}/members',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefMembers')
                    ),
                    array(
                        'path' => '/{:mainId}/members',
                        'method' => array('POST'),
                        'values' => array('action' => 'addMemberToMe')
                    ),
                    array(
                        'path' => '/{:mainId}/members/{:xrefId}',
                        'method' => array('DELETE'),
                        'values' => array('action' => 'removeMemberFromMe')
                    ),
                    array(
                        'path' => '/{:mainId}/members/{:xrefId}',
                        'method' => array('PUT'),
                        'values' => array('action' => 'deactivateMemberFromMe')
                    )
                )
            )
        ),


        /**
         * Members
         */
        $wwwRoot . 'members' => array(
            'name_prefix' => 'members.',
            'secure' => $requireHttps,
            'params' => array(
                'mainId' => '(\d+)',
                'auxId' => '(\d+)',
                'xrefId' => '(\d+)'
            ),
            'values' => array(
                'controller' => 'memberController',
                'auxId' => ''
            ),
            'routes' => array_merge(
                $dirControllerRoutes,
                array(
                    'factions' => array(
                        'path' => '/{:mainId}/factions',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefFactions')
                    ),
                    array(
                        'path' => '/{:mainId}/factions',
                        'method' => array('POST'),
                        'values' => array('action' => 'addMeToFaction')
                    ),
                    array(
                        'path' => '/{:mainId}/factions/{:xrefId}',
                        'method' => array('DELETE'),
                        'values' => array('action' => 'removeMeFromFaction')
                    ),
                    array(
                        'path' => '/{:mainId}/factions/{:xrefId}',
                        'method' => array('PUT'),
                        'values' => array('action' => 'deactivateMeFromFaction')
                    ),
                    'history' => array(
                        'path' => '/{:mainId}/history',
                        'method' => array('GET'),
                        'values' => array('action' => 'getXrefHistoricFactions')
                    )
                )
            )
        ),


        /**
         * Member invoices
         */
        $wwwRoot . 'member-invoices' => array(
            'secure' => $requireHttps,
            'values' => array('controller' => 'memberInvoiceController'),
            'params' => array('id' => '(\d+)'),
            'routes' => array(
                'member-invoices' => array(
                    'path' => '/{:id}',
                    'method' => array('GET'),
                    'values' => array('action' => 'read')
                ),
                array(
                    'path' => '/{:id}',
                    'method' => array('PUT'),
                    'values' => array('action' => 'update')
                ),
                array(
                    'path' => '/{:id}',
                    'method' => array('DELETE'),
                    'values' => array('action' => 'delete')
                )
            )
        ),


        /**
         * Accountant
         */
        $wwwRoot . 'accountant' => array(
            'name_prefix' => 'accountant.',
            'secure' => $requireHttps,
            'values' => array('controller' => 'accountantController'),
            'routes' => array(
                'read' => array(
                    'path' => '{:void:()}',
                    'method' => array('GET'),
                    'values' => array('action' => 'read')
                ),
                'kml' => array(
                    'path' => '/kml',
                    'method' => array('GET'),
                    'values' => array('action' => 'exportTemplates')
                ),
                array(
                    'path' => '/kml',
                    'method' => array('POST'),
                    'values' => array('action' => 'importTemplates')
                ),
                'accounts' => array(
                    'path' => '/accounts',
                    'method' => array('GET'),
                    'values' => array('action' => 'exportAccounts')
                ),
                array(
                    'path' => '/accounts',
                    'method' => array('POST'),
                    'values' => array('action' => 'importAccounts')
                ),
                'bill' => array(
                    'path' => '/bill',
                    'method' => array('POST'),
                    'values' => array('action' => 'billMembers')
                ),
                'print' => array(
                    'path' => '/print',
                    'method' => array('POST'),
                    'values' => array('action' => 'printInvoicesCsv')
                ),
                'export' => array(
                    'path' => '/export',
                    'method' => array('POST'),
                    'values' => array('action' => 'exportInvoices')
                ),
            )
        ),


        /**
         * File upload/download
         */
        $wwwRoot . 'files' => array(
            'secure' => $requireHttps,
            'values' => array('controller' => 'fileController'),
            'routes' => array(
                'upload' => array(
                    'path' => '/upload',
                    'method' => array('POST'),
                    'values' => array('action' => 'upload')
                ),
                'download' => array(
                    'path' => '/download/{:file:([^/]+)}',
                    'method' => array('GET', 'HEAD'),
                    'values' => array('action' => 'download')
                )
            )
        )
    );

    return new Map(new RouteFactory, $routes);
});