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
 * Build json object from multiple entities
 *
 * @package mreg\View
 */
class JsonListView
{

    /**
     * Flag if data should be formatted for readability
     *
     * @var bool
     */
    private $_bReadable = FALSE;


    /**
     * Array of items
     *
     * @var array
     */
    private $_items = array();


    /**
     * Array of links
     *
     * @var array
     */
    private $_links = array();


    /**
     * Set item to list
     *
     * @param Model $item
     * @param string $url
     * @param array $append Array of data to append. Mount points as keys,
     * data as values.
     *
     * @return JsonView Instance for chaining
     */
    public function setItem(Model $item, $url, array $append = NULL)
    {
        $parser = new JsonView($item);
        $parser->setLink('self', $url);
        $parser->setLink('edit', $url);
        if ($append) {
            foreach ($append as $key => $data) {
                $parser->append($key, $data);
            }
        }
        $this->_items[] = $parser->getData();
        
        return $this;
    }


    /**
     * Set entity links
     *
     * @param array $links Associative array with link relations as keys and
     * urls as values
     *
     * @return JsonView Instance for chaining
     */
    public function setLinks(array $links)
    {
        $this->_links = $links;

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
     * Export contents to response
     *
     * @return Response
     */
    public function getResponse()
    {
        $response = $this->buildResponse();
        if ($this->_bReadable) {
            $json = JsonFormatter::format($this->getData());
        } else {
            $json = json_encode($this->getData());
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
        if (empty($this->_links)) {
            
            return $this->_items;
        }
        
        return array(
            'links' => $this->_links,
            'items' => $this->_items
        );
    }


    /**
     * Get items loaded in view
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
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
        foreach ($this->_links as $rel => $link) {
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