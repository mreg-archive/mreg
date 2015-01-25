<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Mapper
 */

namespace mreg\Mapper;

use itbz\datamapper\pdo\Mapper;
use itbz\datamapper\pdo\table\Table;
use itbz\datamapper\pdo\Search;
use mreg\Model\Aux\Revision;

/**
 * Find associated revision data
 *
 * @package mreg\Mapper
 */
class RevisionMapper extends Mapper
{
    /**
     * Name of reference table
     *
     * @var string
     */
    private $_refTable = '';

    /**
     * Find associated revision data
     *
     * @param Table $table
     * @param Revision $prototype 
     */
    public function __construct(Table $table, Revision $prototype)
    {
        parent::__construct($table, $prototype);
    }

    /**
     * Set name of reference table
     *
     * @param string $refTable
     *
     * @return RevisionMapper Instance for chaining
     */
    public function setRefTable($refTable)
    {
        assert('is_string($refTable)');
        $this->_refTable = $refTable;
        
        return $this;
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
        assert('is_string($id)');
        assert('is_string($column)');
        assert('is_string($table)');

        $conditions = array(
            'ref_table' => $this->_refTable,
            'ref_id' => $id
        );

        if ($column) {
            $conditions['ref_column'] = $column;
        }

        if ($table) {
            $conditions['ref_table'] = $table;
        }

        if (!$search) {
            $search = new Search();
        }
    
        $revs = array();
        foreach ($this->findMany($conditions, $search) as $revision) {
            $revs[] = $revision->export();
        }
        
        return $revs;
    }
}
