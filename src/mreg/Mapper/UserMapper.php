<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Mapper
 */

namespace mreg\Mapper;

use itbz\datamapper\pdo\access\AcTable;
use itbz\datamapper\ModelInterface;
use mreg\Model\Sys\User;
use mreg\NullObject\NullDate;

/**
 * Maps mreg users to dabase rows
 *
 * @package mreg\Mapper
 */
class UserMapper extends AccessMapper
{
    /**
     * Number of failed auths yielding user being blocked
     *
     * @var integer
     */
    private $_blockAfterFailures = 0;

    /**
     * Number of months after witch user is considered inactive
     *
     * @var integer
     */
    private $_blockAfterInactive = 0;

    /**
     * Password lifetime in months
     *
     * @var integer
     */
    private $_pswdLifeSpan = 0;

    /**
     * Warn message
     *
     * @var string
     */
    private $_warning = '';

    /**
     * Maps mreg users to dabase rows
     *
     * @param AcTable $table
     * @param User $prototype
     */
    public function __construct(AcTable $table, User $prototype)
    {
        parent::__construct($table, $prototype);
    }

    /**
     * Force group user-edit on inserts
     *
     * @param ModelInterface $mod
     * @param array $use
     *
     * @return ExpressionSet
     */
    public function extractForCreate(ModelInterface $mod, array $use = NULL)
    {
        $data = $this->extractArray($mod, self::CONTEXT_CREATE, $use);
        $data[self::GROUP_FIELD] = 'user-edit';
        
        return $this->arrayToExprSet($data);
    }

    /**
     * Update counters after invalid authentication
     *
     * @param User $user
     * 
     * @return int Number of affected rows
     */
    public function tickInvalidAuth(User $user)
    {
        $user->incrementInvalidAuths();
        $user->setModifiedBy('sys');

        return $this->update($user);
    }

    /**
     * Update counters after valid authentication
     *
     * @param User $user
     * 
     * @return int Number of affected rows
     */
    public function tickValidAuth(User $user)
    {
        $user->rotateLoginDates();
        $user->incrementLogins();
        $user->setModifiedBy('sys');
        $user->setInvalidAuths(0);

        return $this->update($user);
    }

    /**
     * Update db using primary key as conditions clause.
     *
     * @param ModelInterface $user
     *
     * @return int Number of affected rows
     */
    public function update(ModelInterface $user)
    {
        if (!$user->isActive() || $user->isRoot()) {

            return parent::update($user);
        }

        // Block user after x number of unvalid auths
        if ($this->getBlockAfterFailures() != 0) {
            if ($user->getInvalidAuths() >= $this->getBlockAfterFailures()) {
                $user->setStatus(User::STATUS_MULTIPLE_ERRORS);
            }
        }

        // Block user after inactiveness
        if ($this->getBlockAfterInactive() != 0) {
            $lastLogin = $user->getLoginBeforeLast();
            if (!$lastLogin instanceof NullDate) {
                $limit = strtotime("-{$this->getBlockAfterInactive()} month");
                if ($lastLogin->getTimestamp() < $limit) {
                    $user->setStatus(User::STATUS_INACTIVE);
                }
            }
        }

        // Block user if password expired
        if ($this->getPswdLifeSpan() != 0) {
            // Calculate age of password in months
            $diff = $user->getPswdCreated()->diff(new \DateTime);
            $age = ($diff->format('%y') * 12) + $diff->format('%m');

            // Block
            if ($age >= $this->getPswdLifeSpan()) {
                $user->setStatus(User::STATUS_EXPIRED_PSWD);
            
            // Warn when only one month is left
            } elseif ($age == $this->getPswdLifeSpan()-1) {
                $this->setWarning('Ditt lösenord går ut om mindre än en månad');
            }
        }

        return parent::update($user);
    }

    /**
     * Get warning
     *
     * @return string
     */
    public function getWarning()
    {
        return $this->_warning;
    }

    /**
     * Set warning
     *
     * @param string $warning
     *
     * @return void
     */
    public function setWarning($warning)
    {
        assert('is_string($warning)');
        $this->_warning = $warning;
    }

    /**
     * Get number of failed auths yielding user being blocked
     *
     * @return int
     */
    public function getBlockAfterFailures()
    {
        return $this->_blockAfterFailures;
    }

    /**
     * Set number of failed auths yielding user being blocked
     *
     * @param int $blockAfterFailures
     *
     * @return void
     */
    public function setBlockAfterFailures($blockAfterFailures)
    {
        assert('is_int($blockAfterFailures)');
        $this->_blockAfterFailures = $blockAfterFailures;
    }

    /**
     * Get number of months after witch user is considered inactive
     *
     * @return int
     */
    public function getBlockAfterInactive()
    {
        return $this->_blockAfterInactive;
    }

    /**
     * Set number of months after witch user is considered inactive
     *
     * @param int $blockAfterInactive
     *
     * @return void
     */
    public function setBlockAfterInactive($blockAfterInactive)
    {
        assert('is_int($blockAfterInactive)');
        $this->_blockAfterInactive = $blockAfterInactive;
    }

    /**
     * Get password lifetime in months
     *
     * @return int
     */
    public function getPswdLifeSpan()
    {
        return $this->_pswdLifeSpan;
    }

    /**
     * Set password lifetime in months
     *
     * @param int $pswdLifeSpan
     *
     * @return void
     */
    public function setPswdLifeSpan($pswdLifeSpan)
    {
        assert('is_int($pswdLifeSpan)');
        $this->_pswdLifeSpan = $pswdLifeSpan;
    }
}
