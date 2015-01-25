<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;

use Pimple;
use itbz\httpio\Response;
use itbz\httpio\Request;
use mreg\NullObject\AnonymousUser;
use PDOException;
use itbz\datamapper\pdo\access\AccessDeniedException;
use itbz\datamapper\exception\DataNotFoundException;
use mreg\Exception\HTTP\HttpException;
use mreg\Exception\HTTP\NotFoundException;

/**
 * The mreg application
 *
 * @package mreg
 */
class Application
{

    /**
     * Dependency injection container
     *
     * @var Pimple
     */
    private $_container;


    /**
     * Inject dependency container
     *
     * @param Pimple $container
     */
    public function __construct(Pimple $container)
    {
        $this->_container = $container;
    }


    /**
     * Dispatch application
     *
     * Routes to controllers, writes to logs and authenticates users
     *
     * @param Request $request
     * @param array $server Server and execution environment information
     *
     * @return Response
     */
    public function dispatch(Request $request, array $server)
    {
        try {
            $this->setErrorHandler();
            $this->beginTransaction();

            $settings = $this->_container['settings'];
            $userBuilder = $this->_container['userBuilder'];

            // Enable fake authentication
            if (
                $settings->get('auth.enableFakeAuth')
                && !$request->headers->is('REFERER')
                && ($server['SERVER_ADDR'] == $request->getIp())
            ) {
                $this->logInfo('Using fake authentication');
                $userBuilder->enableFakeAuth('root');
            }
            
            // Set user agent
            $userBuilder->setUserAgent(
                $request->headers->get('USER-AGENT', FILTER_SANITIZE_STRING)
            );

            $userBuilder->setSingleSession(
                (bool)$settings->get('auth.checkSingeSession')
            );

            if ($request->headers->is('Authorization')) {
                $this->logDebug('Attempting HTTP-authorization');
                $userBuilder->setAuthHeader(
                    $request->headers->get(
                        'Authorization',
                        FILTER_SANITIZE_STRING
                    )
                );
            }

            // Set fingerprint
            foreach (array('query', 'body') as $cont) {
                if ($request->$cont->is('fingerprint')) {
                    $this->logDebug("Fingerprint found in $cont");
                    $userBuilder->setFingerprint(
                        $request->$cont->get(
                            'fingerprint',
                            FILTER_SANITIZE_STRING
                        )
                    );
                }
            }

            $user = $userBuilder->getUser();

            // Return if user is not authenticated
            if ($user instanceof AnonymousUser) {
                $this->commit();
                $this->restoreErrorHandler();

                $msg = $userBuilder->getError();
                if (!$msg) {
                    $msg = "Autentisering krävs";
                }

                return new Response(
                    json_encode($msg),
                    401,
                    array(
                        'Content-Type' => 'application/json',
                        'WWW-Authenticate' => 'BASICX realm="mreg"'
                    )
                );
            }

            // Set user to DI
            $this->_container['user'] = $user;

            $map = $this->_container['routes'];
            $route = $map->match($request->getUri(), $server);
            
            if ($route) {
                $this->logDebug("Found route '{$route->path}'");

                $controller = $this->_container[$route->values['controller']];
                $action = $route->values['action'];
                
                $dispatch = new Dispatch(
                    $settings,
                    $route,
                    $map,
                    $request,
                    $this->_container['session'],
                    $user
                );
                
                $response = $controller->$action($dispatch);
                
                if ($fingerprint = $userBuilder->getNewFingerprint()) {
                    $this->logDebug("New fingerprint created");
                    $response->setHeader('X-Session-Fingerprint', $fingerprint);
                }
                
                $this->commit();
                $this->restoreErrorHandler();

                $this->logInfo(
                    sprintf(
                        '%s::%s executed successfully',
                        get_class($controller),
                        $action
                    )
                );
                
                // Display userBuilder warning
                if ($userBuilder->isError()) {
                    $response->addWarning($userBuilder->getError());
                }

                return $response;
            }        

            throw new NotFoundException('Resursen finns inte');

        }

        // See collection of mreg HTTP exceptions
        catch (HttpException $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError($e->getMessage());
            $response = new Response(
                json_encode($e->getMessage()),
                $e->getCode(),
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // Access denied to authenticated user
        catch (AccessDeniedException $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logInfo($e->getMessage());
            $response = new Response(
                json_encode($e->getMessage()),
                403,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning('Tillträde ej tillåtet');

            return $response;
        }

        // DataMapper was unable to find requested content
        catch (DataNotFoundException $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logInfo((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                404,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning('Resursen finns inte');

            return $response;
        }

        // DataMapper base exception
        catch (\itbz\datamapper\Exception $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                500,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // STB base exception
        catch (\itbz\stb\Exception $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                500,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // httpio base exception
        catch (\itbz\httpio\Exception $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                500,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // PDOExceptions are errors. If PDOException is tolerable it should be
        // transformed to some other Exception before this point
        catch (PDOException $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                500,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // Mreg base exception. Represents a wide range of error states. Log
        // as error and transform tolerable exceptions as they emerge.
        catch (Exception $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logError((string)$e);
            $response = new Response(
                json_encode($e->getMessage()),
                500,
                array('Content-Type' => 'application/json')
            );
            $response->addWarning($e->getMessage());

            return $response;
        }

        // All other exceptions trigger alerts (ErrorException included)
        catch (\Exception $e) {
            $this->rollBack();
            $this->restoreErrorHandler();
            $this->logAlert((string)$e);

            return new Response('Internal Server Error', 500);
        }

        // This should never happen
        $this->logAlert('Application unable to generate response');
        return new Response('Internal Server Error', 500);
    }


    /**
     * Log alert message
     *
     * @param string $msg
     * @param array $context
     *
     * @return void
     */
    private function logAlert($msg, array $context = array())
    {
        $this->_container['logger']->addAlert($msg, $context);
    }


    /**
     * Log error message
     *
     * @param string $msg
     * @param array $context
     *
     * @return void
     */
    private function logError($msg, array $context = array())
    {
        $this->_container['logger']->addError($msg, $context);
    }


    /**
     * Log info message
     *
     * @param string $msg
     * @param array $context
     *
     * @return void
     */
    private function logInfo($msg, array $context = array())
    {
        $this->_container['logger']->addInfo($msg, $context);
    }


    /**
     * Log debug message
     *
     * @param string $msg
     * @param array $context
     *
     * @return void
     */
    private function logDebug($msg, array $context = array())
    {
        $this->_container['debugLogger']->addDebug($msg, $context);
    }


    /**
     * Register error handler that throws ErrorExceptions on all errors
     *
     * @return void
     */
    private function setErrorHandler()
    {
        set_error_handler(
            function($errno, $msg, $file, $line){
                throw new \ErrorException($msg, 0, $errno, $file, $line);
            }
        );
    }


    /**
     * Restore error handler to its previous state
     *
     * @return void
     */
    private function restoreErrorHandler()
    {
        restore_error_handler();
    }


    /**
     * Start PDO transaction
     *
     * @return void
     */
    private function beginTransaction()
    {
        $this->_container['pdo']->beginTransaction();
    }


    /**
     * Rollback PDO transaction
     *
     * @return void
     */
    private function rollBack()
    {
        $pdo = $this->_container['pdo'];
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }


    /**
     * Commit PDO transaction
     *
     * @return void
     */
    private function commit()
    {
        $pdo = $this->_container['pdo'];
        if ($pdo->inTransaction()) {
            $pdo->commit();
        }
    }

}