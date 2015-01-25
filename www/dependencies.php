<?php
/**
 * Returns a Pimple DI container with mreg dependencies
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

use itbz\datamapper\pdo\table\MysqlTable;
use itbz\datamapper\pdo\table\Table;
use itbz\datamapper\pdo\Mapper;
use itbz\datamapper\pdo\access\AcMapper;
use itbz\datamapper\pdo\access\AcTable;
use mreg\Mapper\DirMapper;
use itbz\utils\Serializer;

/**
 * Create pimple dependency injection container
 *
 * @return Pimple
 *
 * @uses array $settings Mreg configuration settings
 *
 * @package mreg
 */
return call_user_func(function() use ($settings)
{
    $c = new \Pimple();


    /**
     * Configuration settings
     *
     * @var array
     */
    $c['config'] = $settings;


    /**
     * Authenticated user
     *
     * Owerwrite when user is built
     *
     * @var \mreg\Model\Sys\User
     */
    $c['user'] = NULL;


    /**
     * Shared PDO resource
     *
     * @return \PDO
     */
    $c['pdo'] = $c->share(function($c)
    {
        $pdo = new \PDO(
            'mysql:host=localhost;dbname=' . $c['config']['dbName'],
            $c['config']['dbUser'],
            $c['config']['dbPswd'],
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        return $pdo;
    });


    /**
     * Shared cache resource
     *
     * @return \itbz\Cache\XCacheCacher
     */
    $c['cache'] = $c->share(function($c)
    {
        if (php_sapi_name() === 'cli') {
            return new \itbz\Cache\VoidCacher();
        }

        return new \itbz\Cache\XCacheCacher();
    });


    /**
     * Shared route map resource
     *
     * @return \Aura\Router\Map
     */
    $c['routes'] = $c->share(function($c)
    {
        $wwwRoot = $c['config']['wwwRoot'];
        $requireHttps = $c['config']['requireHttps'];

        return include __DIR__ . DIRECTORY_SEPARATOR . "routes.php";
    });


    /**
     * Shared logger resource
     *
     * @return \Monolog\Logger
     *
     * @todo Mail handler for alerts
     */
    $c['logger'] = $c->share(function($c)
    {
        $logger = new \Monolog\Logger('mreg');
        
        try {
            if ($c['user']) {
                $uname = $c['user']->getName();
            } else {
                $uname = '';
            }
            $userProcessor = new \mreg\Log\UserProcessor($uname);
            $webProcessor = new \Monolog\Processor\WebProcessor;

            // File handler for errors
            $fileHandler = new \Monolog\Handler\StreamHandler(
                $c['config']['logFile'],
                \Monolog\Logger::ERROR
            );
            $fileHandler->pushProcessor($userProcessor);
            $fileHandler->pushProcessor(new \Monolog\Processor\WebProcessor);
            $logger->pushHandler($fileHandler);

            // PDO handler for info
            $pdoHandler = new \mreg\Log\PDOHandler($c['pdo']);
            $pdoHandler->pushProcessor($userProcessor);
            $pdoHandler->pushProcessor($webProcessor);
            $logger->pushHandler($pdoHandler);
        } catch (\Exception $e) {
            // Ignore exceptions, logging should continue
        }

        if ($c['config']['logChrome']) {
            $logger->pushHandler(new \Monolog\Handler\ChromePHPHandler);
        }

        return $logger;
    });


    /**
     * Shared debugg logger resource
     *
     * Enables debug logging before $['user'] is loaded
     *
     * @return \Monolog\Logger
     *
     * @todo Mail handler for alerts
     */
    $c['debugLogger'] = $c->share(function($c)
    {
        $logger = new \Monolog\Logger('debug');

        if ($c['config']['logChrome']) {
            $logger->pushHandler(new \Monolog\Handler\ChromePHPHandler);
        }

        return $logger;
    });


    /**
     * Shared settings resource
     *
     * @return \mreg\Settings
     */
    $c['settings'] = $c->share(function($c)
    {
        // Get settings from cache
        $cache = $c['cache'];
        $cacheKey = 'mreg_settings';
        
        if ($cache->has($cacheKey)) {
            
            return Serializer::unserialize($cache->get($cacheKey));
        }
   
        // Build settings
        $pdo = $c['pdo'];
        $stmt = $pdo->query('SELECT * FROM `sys__Setting`');
        $settings = new \mreg\Settings();
        foreach ($stmt as $row) {
            $settings->set($row['name'], $row['value']);
        }
        $cache->set($cacheKey, Serializer::serialize($settings));
        
        return $settings;
    });


    /**
     * Shared session resource
     *
     * @return \mreg\Auth\Session
     */
    $c['session'] = $c->share(function($c)
    {
        return new \mreg\Auth\Session($c['pdo'], 'sys__Session');
    });


    /**
     * User builder resource
     *
     * @return \mreg\Auth\AuthUserBuilder
     */
    $c['userBuilder'] = function($c)
    {
        return new \mreg\Auth\AuthUserBuilder(
            $c['session'],
            $c['userMapper']
        );
    };


    /**
     * Gravatar resource
     *
     * @return \emberlabs\GravatarLib\Gravatar
     */
    $c['gravatar'] = function($c)
    {
        $gravatar = new \emberlabs\GravatarLib\Gravatar();
        $gravatar->setAvatarSize(80);
        $gravatar->setMaxRating('pg');
        $gravatar->enableSecureImages();
        
        return $gravatar;
    };


    /**
     * Shared SIE parser resource
     *
     * @return \itbz\stb\Accounting\Formatter\SIE
     */
    $c['sie'] = $c->share(function($c)
    {
        $sie = new \itbz\stb\Accounting\Formatter\SIE();
        $sie->setProgram('mreg', $c['config']['version']);
        if ($c['user']) {
            $sie->setCreator($c['user']->getName());
            if ($c['user']->getAccountingFor()) {
                $sie->setCompany($c['user']->getAccountingFor());
            }
        }

        return $sie;
    });


    /**
     * Accountant resource
     *
     * @return \mreg\Economy\Accountant
     */
    $c['accountant'] = function($c)
    {
        return new \mreg\Economy\Accountant($c['sie']);
    };


    /**
     * Ledger resource
     *
     * @return \mreg\Economy\Ledger
     */
    $c['ledger'] = function($c)
    {
        if (!$c['user']) {
            $msg = "User must be set to fetch ledger";
            throw new \mreg\Exception($msg);
        }
        try {
            // Get the faction user is accounting for
            $factionMapper = $c['factionMapper'];
            $factionMapper->setAuthUser($c['user']);
            $faction = $factionMapper->findByPk($c['user']->getAccountingFor());

            // Get accountant for this faction
            $accountantMapper = $c['accountantMapper'];
            $accountant = $accountantMapper->findByPk(
                $faction->getAccountantId()
            );
        } catch (\itbz\datamapper\exception\DataNotFoundException $e) {
            $msg = "Bokhållare saknas";
            throw new \mreg\Exception\HTTP\NotFoundException($msg);
        }

        $memberMapper = $c['memberMapper'];
        $memberMapper->setAuthUser($c['user']);

        $invoiceMapper = $c['memberInvoiceMapper'];
        $invoiceMapper->setUser($c['user']->getName(), $c['user']->getGroups());
        
        return new \mreg\Economy\Ledger(
            $accountant,
            $faction,
            $c['factionMemberXrefMapper'],
            $memberMapper,
            $invoiceMapper,
            $c['sie']
        );
    };


    /**
     * Member invoice mapper resource
     *
     * @return AcMapper
     */
    $c['memberInvoiceMapper'] = $c->share(function($c)
    {
        return new AcMapper(
            new AcTable(
                'eco__MemberInvoice',
                $c['pdo'],
                'root',
                'treasurer',
                0770
            ),
            new \mreg\Economy\MemberInvoice
        );
    });


    /**
     * Shared address mapper resource
     *
     * @return Mapper
     */
    $c['addressMapper'] = $c->share(function($c)
    {
        $table = new MysqlTable('aux__Address', $c['pdo']);
 
        $country = new \itbz\phpcountry\Country;
        $country->setLang('en');
        $addr = new \itbz\phplibaddress\Address($country);
        $addr->setCountryOfOrigin('SE');
        $composer = new \itbz\phplibaddress\Composer\Sv(
            new \itbz\phplibaddress\Composer\Breviator,
            $addr
        );
        $prototype = new \mreg\Model\Aux\Address($composer);

        return new Mapper($table, $prototype);
    });


    /**
     * Shared mail mapper resource
     *
     * @return Mapper
     */
    $c['mailMapper'] = $c->share(function($c)
    {
        $table = new MysqlTable('aux__Mail', $c['pdo']);
        $prototype = new \mreg\Model\Aux\Mail(new \EmailAddressValidator());

        return new Mapper($table, $prototype);
    });


    /**
     * Shared phone mapper resource
     *
     * @return Mapper
     */
    $c['phoneMapper'] = $c->share(function($c)
    {
        $table = new MysqlTable('aux__Phone', $c['pdo']);

        $phpCountry = new \itbz\phpcountry\Country;
        $phpCountry->setLang('sv');

        $countryLib = new \itbz\phplibphone\Library\Countries($phpCountry);

        $parser = new \itbz\phplibphone\Number($countryLib, 46);
        $parser->setAreaLib(new \itbz\phplibphone\Library\AreasSeSv);

        $prototype = new \mreg\Model\Aux\Phone($parser);

        return new Mapper($table, $prototype);
    });


    /**
     * Revision mapper resource
     *
     * @return \mreg\Mapper\RevisionMapper
     */
    $c['revisionMapper'] = function($c)
    {
        $table = new Table('aux__Revision', $c['pdo']);
        $table->setColumns(
            array(
                'id',
                'tModified',
                'modifiedBy',
                'ref_table',
                'ref_id',
                'ref_column',
                'old_value',
                'new_value'
            )
        );

        return new \mreg\Mapper\RevisionMapper(
            $table,
            new \mreg\Model\Aux\Revision
        );
    };


    /**
     * System group mapper resource
     *
     * @return AcMapper
     */
    $c['systemGroupMapper'] = function($c)
    {
        return new AcMapper(
            new AcTable(
                'sys__Group',
                $c['pdo'],
                'root',
                'user-edit',
                0770
            ),
            new \mreg\Model\Sys\SystemGroup
        );
    };


    /**
     * User mapper resource
     *
     * @return AcMapper
     */
    $c['userMapper'] = function($c)
    {
        $settings = $c['settings'];

        $hasher = new \Phpass\Hash;
        $strength = new \Phpass\Strength(new \Phpass\Strength\Adapter\Wolfram);
        $pswd = new \mreg\Auth\Password($hasher, $strength);
        $pswd->setStrongLimit(intval($settings->get('auth.pswdMinEntropy')));
        $user = new \mreg\Model\Sys\User($pswd); 

        $mapper = new \mreg\Mapper\UserMapper(
            new AcTable(
                'sys__User',
                $c['pdo'],
                'root',
                'user-edit',
                0770
            ),
            $user
        );

        $mapper->setBlockAfterFailures(
            intval($settings->get('auth.blockAfterFailures'))
        );
        $mapper->setBlockAfterInactive(
            intval($settings->get('auth.blockAfterInactive'))
        );
        $mapper->setPswdLifeSpan(
            intval($settings->get('auth.pswdLifeSpan'))
        );

        return $mapper;
    };


    /**
     * Accountant mapper resource
     *
     * @return \mreg\Mapper\AccountantMapper
     */
    $c['accountantMapper'] = function($c)
    {
        $table = new Table('eco__Accountant', $c['pdo']);
        $table->setColumns(
            array(
                'id',
                'accounts',
                'channels',
                'templates',
                'debits',
                'parent'
            )
        );

        return new \mreg\Mapper\AccountantMapper($table, $c['accountant']);
    };


    /**
     * Shared faction mapper resource
     *
     * @return DirMapper
     */
    $c['factionMapper'] = $c->share(function($c)
    {
        return new DirMapper(
            new AcTable(
                'dir__Faction',
                $c['pdo'],
                'root',
                'group-edit',
                0770
            ),
            new \mreg\Model\Dir\Faction(),
            $c['addressMapper'],
            $c['mailMapper'],
            $c['phoneMapper'],
            $c['revisionMapper']->setRefTable('dir__Faction')
        );
    });


    /**
     * Shared member mapper resource
     *
     * @return DirMapper
     */
    $c['memberMapper'] = $c->share(function($c)
    {
        return new DirMapper(
            new AcTable(
                'dir__Member',
                $c['pdo'],
                'root',
                'group-edit',
                0770
            ),
            new \mreg\Model\Dir\Member(),
            $c['addressMapper'],
            $c['mailMapper'],
            $c['phoneMapper'],
            $c['revisionMapper']->setRefTable('dir__Member')
        );
    });


    /**
     * Faction to faction xref mapper resource
     *
     * @return \mreg\Mapper\XrefMapper
     */
    $c['factionFactionXrefMapper'] = function($c)
    {
        $factionMapper = $c['factionMapper'];
        
        return new \mreg\Mapper\XrefMapper(
            new MysqlTable('xref__Faction_Faction', $c['pdo']),
            new \mreg\Model\Xref\Xref,
            $factionMapper,
            $factionMapper
        );
    };


    /**
     * Faction to member xref mapper resource
     *
     * @return \mreg\Mapper\XrefMapper
     */
    $c['factionMemberXrefMapper'] = function($c)
    {
        return new \mreg\Mapper\XrefMapper(
            new MysqlTable('xref__Faction_Member', $c['pdo']),
            new \mreg\Model\Xref\Xref,
            $c['factionMapper'],
            $c['memberMapper']
        );
    };


    /**
     * Faction controller resource
     *
     * @return \mreg\Controller\FactionController
     */
    $c['factionController'] = function($c)
    {
        return new \mreg\Controller\FactionController(
            $c['factionMapper'],
            new \mreg\Tree\FactionTreeFactory(),
            $c['cache'],
            'mreg_faction_tree"',
            $c['factionFactionXrefMapper'],
            $c['factionMemberXrefMapper']
        );
    };


    /**
     * Member controller resource
     *
     * @return \mreg\Controller\MemberController
     */
    $c['memberController'] = function($c)
    {
        return new \mreg\Controller\MemberController(
            $c['memberMapper'],
            $c['factionMemberXrefMapper'],
            $c['memberInvoiceMapper']
        );
    };


    /**
     * Accountant controller resource
     *
     * @return \mreg\Controller\AccountantController
     */
    $c['accountantController'] = function($c)
    {
        return new \mreg\Controller\AccountantController(
            $c['ledger'],
            $c['accountantMapper'],
            $c['config']['uploadDir'],
            $c['config']['downloadDir']
        );
    };


    /**
     * Accountant controller resource
     *
     * @return \mreg\Controller\CreateAccountantController
     */
    $c['createAccountantController'] = function($c)
    {
        $defaultTemplates = file_get_contents(
            $c['config']['contentDir'] . 'economy-data/konteringsmallar.kml'
        );

        $defaultAccounts = file_get_contents(
            $c['config']['contentDir'] . 'economy-data/LS-Baskontoplan.kp'
        );

        $defaultChannels = file_get_contents(
            $c['config']['contentDir'] . 'economy-data/kanaler-standard.base64'
        );
        $defaultChannels = Serializer::unserialize($defaultChannels);

        $defaultDebits = file_get_contents(
            $c['config']['contentDir'] . 'economy-data/avgifter-standard.base64'
        );
        $defaultDebits = Serializer::unserialize($defaultDebits);

        return new \mreg\Controller\CreateAccountantController(
            $c['accountantMapper'],
            $c['factionMapper'],
            $c['factionFactionXrefMapper'],
            $defaultTemplates,
            $defaultAccounts,
            $defaultChannels,
            $defaultDebits
        );
    };


    /**
     * Jsclient controller resource
     *
     * @return \mreg\Controller\ClientController
     */
    $c['clientController'] = function($c)
    {
        return new \mreg\Controller\ClientController();
    };


    /**
     * File upload/download controller resource
     *
     * @return \mreg\Controller\FileController
     */
    $c['fileController'] = function($c)
    {
        return new \mreg\Controller\FileController(
            $c['config']['uploadDir'],
            $c['config']['downloadDir']
        );
    };


    /**
     * System group controller resource
     *
     * @return \mreg\Controller\SystemController
     */
    $c['systemGroupController'] = function($c)
    {
        return new \mreg\Controller\SystemController($c['systemGroupMapper']);
    };


    /**
     * User controller resource
     *
     * @return \mreg\Controller\SystemController
     */
    $c['userController'] = function($c)
    {
        return new \mreg\Controller\SystemController($c['userMapper']);
    };


    /**
     * Search controller resource
     *
     * @return \mreg\Controller\SearchController
     */
    $c['searchController'] = function($c)
    {
        return new \mreg\Controller\SearchController($c['pdo']);
    };


    /**
     * Cache controller resource
     *
     * @return \mreg\Controller\CacheController
     */
    $c['cacheController'] = function($c)
    {
        return new \mreg\Controller\CacheController($c['cache']);
    };


    /**
     * Member invoice controller resource
     *
     * @return \mreg\Controller\InvoiceController
     */
    $c['memberInvoiceController'] = function($c)
    {
        return new \mreg\Controller\InvoiceController(
            $c['memberInvoiceMapper'],
            $c['revisionMapper']->setRefTable('eco__MemberInvoice'),
            $c['ledger']
        );
    };


    return $c;
});