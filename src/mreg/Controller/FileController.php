<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Response;
use itbz\httpio\Request;
use mreg\Dispatch;
use Aura\Router\Map;
use mreg\Exception\HTTP\NotFoundException;
use mreg\Exception;


/**
 * File upload/download controller 
 *
 * @package mreg\Controller
 */
class FileController
{

    /**
     * Upload target directory
     *
     * @var string
     */
    private $_uploadDir;


    /**
     * Download temporary directory
     *
     * @var string
     */
    private $_downloadDir;


    /**
     * Set upload directory
     *
     * @param string $uploadDir
     *
     * @param string $downloadDir
     */
    public function __construct($uploadDir, $downloadDir)
    {
        assert('is_string($uploadDir)');
        assert('is_string($downloadDir)');
        $this->_uploadDir = $uploadDir;
        $this->_downloadDir = $downloadDir;
    }


    /**
     * Upload file
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function upload(Dispatch $dispatch)
    {
        $request = $dispatch->getRequest();
        $return = array();
        while ($upload = $request->getNextUpload()) {
            $upload->moveToDir($this->_uploadDir);
            $return[$upload->getTargetName()] = TRUE;
        }
        $response = new Response();
        $response->setContent("<textarea>".json_encode($return)."</textarea>");
        
        return $response;
    }


    /**
     * Download a file previously prepared for download
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws NotFoundException file does not exist
     *
     * @throws Exception if unable to unlink file
     */
    function download(Dispatch $dispatch)
    {
        $basename = $dispatch->getRoute()->values['file'];
        $fname = $this->_downloadDir . DIRECTORY_SEPARATOR . $basename;
        if (!is_readable($fname)) {
            throw new NotFoundException();
        }
        $file = file_get_contents($fname);
        $response = new Response();
        $response->setFile($file, $basename);
        if ($dispatch->getRequest()->isMethod('GET')) {
            if (!@unlink($fname)) {
                throw new Exception('Unable to unlink downloaded file');
            }
        }

        return $response;
    }


    /**
     * Create a temporary file and return link header pointing to file
     *
     * @param Map $map
     *
     * @param string $data
     *
     * @param string $title Title to send in link header
     *
     * @param string $extension Extension of created file (eg. pdf)
     *
     * @param string $ctype Content type (eg. application/pdf)
     *
     * @return string Link header contents
     *
     * @throws Exception if unable to write to downloads dir
     */
    public function prepareDownload(
        Map $map,
        $data,
        $title,
        $extension = '',
        $ctype = ''
    )
    {
        assert('is_string($data)');
        assert('is_string($title)');
        assert('is_string($extension)');
        assert('is_string($ctype)');
        if ($ctype) {
            $ctype = "type=\"$ctype\";";
        }
        $filename = uniqid($this->_downloadDir . DIRECTORY_SEPARATOR);
        if ($extension) {
            $filename .= '.' . $extension;
        }
        if (!@file_put_contents($filename, $data)) {
            throw new Exception('Unable to write to downloads dir');
        }
        $url = $map->generate('download', array('file' => basename($filename)));

        return "<$url>;rel=\"download\";{$ctype}title=\"$title\"";
    }

}
