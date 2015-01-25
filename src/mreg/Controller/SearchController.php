<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Response;
use PDO;
use mreg\Dispatch;

/**
 * Fulltext MyISAM search controller
 *
 * Works diretly with a raw PDO object on the MyISAM search tables
 *
 * @package mreg\Controller
 */
class SearchController
{

    /**
     * PDO instance
     *
     * @var PDO
     */
    private $_pdo;


    /**
     * Fulltext MyISAM search controller
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->_pdo = $pdo;
    }


    /**
     * Controller for performing a serach
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function search(Dispatch $dispatch)
    {
        $request = $dispatch->getRequest();
        // Get search query
        $query = $request->query->get('q', FILTER_SANITIZE_STRING);

        // Create pagination
        if ($request->query->is('startPage')) {
            $page = $request->query->get("startPage", '/^\d+$/');
        } else {
            $page = 1;
        }
        if ($request->query->is('itemsPerPage')) {
            $itemsPerPage = $request->query->get("itemsPerPage", '/^\d+$/');
        } else {
            $itemsPerPage = 1000;
        }
        // Set limit to itemsPerPage + 1, so we can know if there is a next page
        $limit = $itemsPerPage + 1;
        $startIndex = $itemsPerPage * ($page - 1);

        // Build and execute query
        $stmt = $this->_pdo->prepare(
            "SELECT `uri`, `title`, `id`, `type`, `description`
            FROM `search__active` WHERE
            MATCH(`title`, `description`, `misc`, `type`)
            AGAINST (? IN BOOLEAN MODE)
            LIMIT $startIndex, $limit"
        );
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindValue(1, $query, PDO::PARAM_STR);
        $stmt->execute();

        // Build results
        $data = array(
            'title' => 'Sökning ' . strftime('%H:%M'),
            'links' => array(),
            'items' => array()
        );

        $isNextPage = FALSE;
        $count = 0;
        while ($row = $stmt->fetch()) {
            $count++;
            if ($count > $itemsPerPage) {
                $isNextPage = TRUE;
                break;
            }
            $data['items'][] = $row;
        }

        // Create pagination links
        $path = $dispatch->getMap()->generate('search', array());
        $format = $path . "q=%s&startPage=%s&itemsPerPage=%s";
        $query = urlencode($query);
        $data['links']['self'] = sprintf($format, $query, $page, $itemsPerPage);
        $data['links']['first'] = sprintf($format, $query, '1', $itemsPerPage);
        if ($page > 1) {
            $data['links']['prev'] = sprintf(
                $format,
                $query,
                $page - 1,
                $itemsPerPage
            );
        }
        if ($isNextPage) {
            $data['links']['next'] = sprintf(
                $format,
                $query,
                $page + 1,
                $itemsPerPage
            );
        }

        // Create response
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');
        foreach ($data['links'] as $rel => $link) {
            $header = sprintf('%s;rel=%s;type="application/json"', $link, $rel);
            $response->addHeader('Link', $header);
        }
        $json = json_encode($data);
        //$json = \itbz\utils\JsonFormatter::format($data);
        $response->setContent($json);

        return $response;
    }

}
