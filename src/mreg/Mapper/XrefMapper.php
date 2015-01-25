<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Mapper
 */

namespace mreg\Mapper;

use itbz\datamapper\pdo\Mapper;
use itbz\datamapper\pdo\Search;
use itbz\datamapper\pdo\table\Table;
use itbz\datamapper\ModelInterface;
use mreg\Exception;
use mreg\Model\Dir\DirModel;
use DateTime;
use itbz\datamapper\pdo\Expression;
use mreg\Model\Sys\User;

/**
 * Mapper for many-to-many relations
 *
 * @package mreg\Mapper
 */
class XrefMapper extends Mapper
{
    /**
     * Mapper for master xref entities
     *
     * @var DirMapper
     */
    private $_masterMapper;

    /**
     * Mapper for foreign xref entities
     *
     * @var DirMapper
     */
    private $_foreignMapper;

    /**
     * Register DirMappers to manage relations between
     *
     * @param Table $table
     * @param ModelInterface $prototype 
     * @param DirMapper $masterMapper
     * @param DirMapper $foreignMapper
     */
    public function __construct(
        Table $table,
        ModelInterface $prototype,
        DirMapper $masterMapper,
        DirMapper $foreignMapper
    ) {
        parent::__construct($table, $prototype);
        $this->_masterMapper = $masterMapper;
        $this->_foreignMapper = $foreignMapper;
    }

    /**
     * Set authenticated mreg user
     *
     * @param User $authUser
     *
     * @return void
     */
    public function setAuthUser(User $authUser)
    {
        $this->_masterMapper->setAuthUser($authUser);
        $this->_foreignMapper->setAuthUser($authUser);
    }

    /**
     * Get authenticated mreg user
     *
     * @return User
     */
    public function getAuthUser()
    {
        return $this->_masterMapper->getAuthUser();
    }

    /**
     * Find referenced foreign models
     *
     * @param DirModel $model Referring model
     * @param Search $search
     * @param Expression $state Expression for requested xref states
     *
     * @return array Array of arrays. Each subarray contains the linking xref
     * object and referenced foreign object.
     */
    public function findXrefForeigns(
        DirModel $model,
        Search $search,
        Expression $state = NULL
    ) {
        $conditions = array('master_id' => $model->getId());
        if ($state) {
            $conditions[] = $state;
        }
        $return = array();
        foreach ($this->findMany($conditions, $search) as $xref) {
            $return[] = array(
                $xref,
                $this->_foreignMapper->findByPk($xref->getForeignId())
            );
        }

        return $return;
    }

    /**
     * Find referenced master models
     *
     * @param DirModel $model Referring model
     * @param Search $search
     * @param Expression $state Expression for requested xref states
     *
     * @return array Array of arrays. Each subarray contains the linking xref
     * object and referenced master object.
     */
    public function findXrefMasters(
        DirModel $model,
        Search $search,
        Expression $state = NULL
    ) {
        $conditions = array('foreign_id' => $model->getId());
        if ($state) {
            $conditions[] = $state;
        }
        $return = array();
        foreach ($this->findMany($conditions, $search) as $xref) {
            $return[] = array(
                $xref,
                $this->_masterMapper->findByPk($xref->getMasterId())
            );
        }

        return $return;
    }

    /**
     * Link reffering model with referenced model id
     *
     * Model will be master record, id foreign and state OK
     *
     * @param scalar $masterId Id of referring model
     * @param scalar $foreignId Id of model to be referenced
     * @param DateTime $since Optional link creation date
     *
     * @return int Number of rows affected rows
     */
    public function link($masterId, $foreignId, DateTime $since = NULL)
    {
        $xref = $this->getNewModel();
        $xref->setMasterId($masterId);
        $xref->setForeignId($foreignId);
        $xref->setState('OK');
        if ($since) {
            $xref->setSince($since);
        }

        return $this->save($xref);
    }

    /**
     * Unlink reffering model with referenced model id
     *
     * Removes link where model is master, id foreign and state OK
     *
     * @param scalar $masterId Id of referring model
     * @param scalar $foreignId Id of model to be referenced
     *
     * @return int Number of rows affected in db
     */
    public function unlink($masterId, $foreignId)
    {
        $xref = $this->find(
            array(
                'master_id' => $masterId,
                'foreign_id' => $foreignId,
                'state' => 'OK'
            )
        );

        return $this->delete($xref);
    }

    /**
     * Deactivate reffering model with referenced model id
     *
     * Set new state to link where model is master, id foreign and state OK
     *
     * @param scalar $masterId Id of referring model
     * @param scalar $foreignId Id of model to be referenced
     * @param string $newState State to set
     * @param string $comment Comment describing state change
     * @param DateTime $unto Optional link deactivation date
     *
     * @return int Number of rows affected in db
     */
    public function deactivate(
        $masterId,
        $foreignId,
        $newState = 'HISTORIC',
        $comment = '',
        DateTime $unto = NULL
    ) {
        $xref = $this->find(
            array(
                'master_id' => $masterId,
                'foreign_id' => $foreignId,
                'state' => 'OK'
            )
        );
        $xref->deactivate($newState, $comment, $unto);
        
        return $this->save($xref);
    }

    /**
     * Unlink all referenced models
     *
     * @param scalar $id Id of model to be unlinked
     *
     * @return int Number of rows affected in db
     */
    public function unlinkAll($id)
    {
        $count = 0;
        $iterator = $this->findMany(
            array('foreign_id' => $id),
            new Search()
        );
        foreach ($iterator as $xref) {
            $count += $this->delete($xref);
        }
        $iterator = $this->findMany(
            array('master_id' => $id),
            new Search()
        );
        foreach ($iterator as $xref) {
            $count += $this->delete($xref);
        }
        
        return $count;
    }
}
