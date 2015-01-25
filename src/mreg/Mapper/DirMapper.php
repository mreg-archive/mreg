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
use itbz\datamapper\pdo\Mapper;
use itbz\datamapper\pdo\Search;
use mreg\Exception;
use mreg\Model\Dir\DirModel;
use mreg\Model\Aux\AuxModel;
use mreg\Model\Model;
use mreg\Model\Sys\User;

/**
 * Maps basic Mreg entities to dabase rows
 *
 * @package mreg\Mapper
 */
class DirMapper extends AccessMapper implements \mreg\AssocInterface
{
    /**
     * Mappers to associated content
     *
     * Use ASSOC constants to fetch mapper
     *
     * @var array
     */
    private $_assocMappers;

    /**
     * Maps basic Mreg entities to dabase rows
     *
     * @param AcTable $table
     * @param ModelInterface $prototype 
     * @param Mapper $addressMapper
     * @param Mapper $mailMapper
     * @param Mapper $phoneMapper
     * @param RevisionMapper $revisionMapper
     */
    public function __construct(
        AcTable $table,
        ModelInterface $prototype,
        Mapper $addressMapper,
        Mapper $mailMapper,
        Mapper $phoneMapper,
        RevisionMapper $revisionMapper
    ) {
        parent::__construct($table, $prototype);
        $this->_assocMappers = array(
            self::ASSOC_ADDRESS => $addressMapper,
            self::ASSOC_MAIL => $mailMapper,
            self::ASSOC_PHONE => $phoneMapper,
            self::ASSOC_REVISION => $revisionMapper
        );
    }

    /**
     * Get a new associated entitiy
     *
     * @param int $mapperId Id of mapper, use ASSOC constants
     *
     * @return AuxModel
     */
    public function getNewAssociated($mapperId)
    {
        return $this->getAssocMapper($mapperId)->getNewModel();
    }

    /**
     * Find revisions associated with table and id
     *
     * @param string $id
     * @param string $column Optional reference column name
     * @param string $table Optional reference table name
     * @param Search $search
     *
     * @return array Array of exported revisions
     */
    public function findRevisions(
        $id,
        $column = '',
        $table = '',
        Search $search = NULL
    ) {
        return $this->getAssocMapper(self::ASSOC_REVISION)->findRevisions(
            $id,
            $column,
            $table,
            $search
        );
    }

    /**
     * Find entities associated with master model
     *
     * @param array $conditions
     * @param Search $search
     * @param int $mapperId Id of mapper, use ASSOC constants
     *
     * @return \Iterator
     */
    public function findManyAssociated(
        array $conditions,
        Search $search,
        $mapperId
    ) {
        $conditions['ref_table'] = $this->table->getName();

        return $this->getAssocMapper($mapperId)->findMany($conditions, $search);
    }

    /**
     * Find entitiy associated with master model
     *
     * @param array $conditions
     * @param int $mapperId Id of mapper, use ASSOC constants
     *
     * @return AuxModel
     */
    public function findAssociated(array $conditions, $mapperId)
    {
        $conditions['ref_table'] = $this->table->getName();

        return $this->getAssocMapper($mapperId)->find($conditions);
    }

    /**
     * Save associated entitiy
     *
     * @param AuxModel $entity
     * @param int $mapperId Id of mapper, use ASSOC constants
     *
     * @return int Number of affected rows
     */
    public function saveAssociated(AuxModel $entity, $mapperId)
    {
        $entity->setRefTable($this->table->getName());
        $entity->setModifiedBy($this->getAuthUser()->getName());
        $entity->setEtag('');

        return $this->getAssocMapper($mapperId)->save($entity);
    }

    /**
     * Delete associated entitiy
     *
     * @param AuxModel $entity
     * @param int $mapperId Id of mapper, use ASSOC constants
     *
     * @return int Number of affected rows
     */
    public function deleteAssociated(AuxModel $entity, $mapperId)
    {
        return $this->getAssocMapper($mapperId)->delete($entity);
    }

    /**
     * Get associated mapper from id
     *
     * @param int $mapperId
     *
     * @return Mapper
     *
     * @throws Exception if mapperId is illegal
     */
    private function getAssocMapper($mapperId)
    {
        if (!isset($this->_assocMappers[$mapperId])) {
            $msg = "Illegal mapper id '$mapperId', use ASSOC contants";
            throw new Exception($msg);
        }

        return $this->_assocMappers[$mapperId];
    }
}
