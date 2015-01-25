<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\View
 */

namespace mreg\View;

use mreg\Model\Model;
use itbz\utils\JsonFormatter;
use itbz\httpio\Response;
use DateTime;

/**
 * Build json object from inputs
 *
 * @package mreg\View
 */
class JsonView
{

    /**
     * Mreg entity
     *
     * @var Model
     */
    private $_entity;


    /**
     * Flag if data should be formatted for readability
     *
     * @var bool
     */
    private $_bReadable = FALSE;


    /**
     * Array of data to send
     *
     * @var array
     */
    private $_data;


    /**
     * Set entity
     *
     * @param Model $entity
     */
    public function __construct(Model $entity = NULL)
    {
        if ($entity) {
            $this->_entity = $entity;
            $this->_data = $this->_entity->export();
            $this->_data['links'] = array();
            $this->setEtag($entity->getEtag());
        } else {
            $this->_data = array('links' => array());
            //$this->setEtag('');
        }
    }


    /**
     * Get loaded entity
     *
     * @return Model
     */
    public function getEntity()
    {
        return $this->_entity;
    }


    /**
     * Set entity etag
     *
     * @param string $etag
     *
     * @return JsonView Instance for chaining
     */
    public function setEtag($etag)
    {
        assert('is_string($etag)');
        $this->_data['etag'] = $etag;

        return $this;
    }


    /**
     * Set entity link
     *
     * @param string $rel Link relation
     * @param string $url
     *
     * @return JsonView Instance for chaining
     */
    public function setLink($rel, $url)
    {
        assert('is_string($rel)');
        assert('is_string($url)');
        $this->_data['links'][$rel] = $url;
        
        return $this;
    }


    /**
     * Set if json output shoudl be formatted for readability
     *
     * @param bool $flag
     *
     * @return JsonView Instance for chaining
     */
    public function setReadable($flag)
    {
        assert('is_bool($flag)');
        $this->_bReadable = $flag;
        
        return $this;
    }


    /**
     * Append data
     *
     * @param string $key Name of field to append to
     * @param mixed $data
     *
     * @return JsonView Instance for chaining
     */
    public function append($key, $data)
    {
        assert('is_string($key)');
        $this->_data[$key] = $data;
        
        return $this;
    }


    /**
     * Export contents to response
     *
     * @return Response
     */
    public function getResponse()
    {
        $response = $this->buildResponse();
        if ($this->_bReadable) {
            $json = JsonFormatter::format($this->_data);
        } else {
            $json = json_encode($this->_data);
        }
        $response->setContent($json);
        
        return $response;
    }


    /**
     * Get view contents
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }


    /**
     * Create a new response object
     *
     * Content-Type, etag and link headers are written to response if present
     *
     * @return Response
     */
    private function buildResponse()
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');
        if (isset($this->_data['etag'])) {
            $response->setHeader('Etag', $this->_data['etag']);
        }
        foreach ($this->_data['links'] as $rel => $link) {
            $header = sprintf(
                '%s;rel=%s;type="application/json"',
                $link,
                $rel
            );
            $response->addHeader('Link', $header);
        }
                
        return $response;
    }

}