<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Mapper
 */

namespace mreg\Mapper;

use itbz\datamapper\pdo\access\AcMapper;
use itbz\datamapper\ModelInterface;
use mreg\Model\Sys\User;
use mreg\NullObject\AnonymousUser;

/**
 * Maps access contolled mreg entities to dabase rows
 *
 * Extends datamapper\AcMapper by handling an authenticated mreg User object
 * 
 * @package mreg\Mapper
 */
class AccessMapper extends AcMapper
{
    /**
     * Authenticated mreg user
     *
     * @var User
     */
    private $_authUser;

    /**
     * Set authenticated mreg user
     *
     * @param User $authUser
     *
     * @return void
     */
    public function setAuthUser(User $authUser)
    {
        $this->_authUser = $authUser;
        $this->setUser($authUser->getName(), $authUser->getGroups());
    }

    /**
     * Get authenticated mreg user
     *
     * @return User
     */
    public function getAuthUser()
    {
        if (isset($this->_authUser)) {

            return $this->_authUser;
        }

        return new AnonymousUser();
    }

    /**
     * Set modified-by if not set
     *
     * Public to simplyfy testing
     *
     * @param ModelInterface $mod
     * @param array $use
     *
     * @return ExpressionSet
     */
    public function extractForUpdate(ModelInterface $mod, array $use = NULL)
    {
        $data = $this->extractArray($mod, self::CONTEXT_UPDATE, $use);
        if (!isset($data['modifiedBy'])) {
            $data['modifiedBy'] = $this->getAuthUser()->getName();
        }

        return $this->arrayToExprSet($data);
    }
}
