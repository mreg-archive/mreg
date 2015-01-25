<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Dir
 */

namespace mreg\Model\Dir;

use itbz\httpio\Request;


/**
 * Mreg dir model base class
 *
 * @package mreg\Model\Dir
 */
abstract class DirModel extends \mreg\Model\AcModel
{

    /**
     * Url associated with this entity
     *
     * @var string
     */
    private $_url = '';


    /**
     * Url to avator associated with this entity
     *
     * @var string
     */
    private $_avatar = '';


    /**
     * Additional notes on faction
     *
     * @var string
     */
    private $_notes = '';


    /**
     * Get array describing addresse
     *
     * @return array
     */
    abstract public function getAddressee();


    /**
     * Load data from mapper into model
     *
     * @param array $data
     *
     * @return void
     */
    public function load(array $data)
    {
        parent::load($data);

        if (isset($data['url'])) {
            $this->setUrl($data['url']);
        }
        if (isset($data['avatar'])) {
            $this->setAvatar($data['avatar']);
        }
        if (isset($data['notes'])) {
            $this->setNotes((string)$data['notes']);
        }
    }


    /**
     * Extract data for datastore
     *
     * @param int $context
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
        return parent::extract($context, $using) + array(
            'url' => $this->getUrl(),
            'avatar' => $this->getAvatar(),
            'notes' => $this->getNotes()
        );
    }


    /**
     * Get url associated with this entity
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }


    /**
     * Set url associated with this entity
     *
     * @param string $url
     *
     * @return void
     */
    public function setUrl($url)
    {
        assert('is_string($url)');
        $this->_url = $url;
    }


    /**
     * Get url to avatar associated with this entity
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->_avatar;
    }


    /**
     * Set url to avatar associated with this entity
     *
     * @param string $avatarUrl
     *
     * @return void
     */
    public function setAvatar($avatarUrl)
    {
        assert('is_string($avatarUrl)');
        $this->_avatar = $avatarUrl;
    }


    /**
     * Get entity notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->_notes;
    }


    /**
     * Set entity notes
     *
     * @param string $notes
     *
     * @return void
     */
    public function setNotes($notes)
    {
        assert('is_string($notes)');
        $this->_notes = $notes;
    }

}