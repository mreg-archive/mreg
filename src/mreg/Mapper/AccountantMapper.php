<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Mapper
 */

namespace mreg\Mapper;

use mreg\Exception;
use mreg\Economy\Accountant;
use mreg\Economy\Channels;
use mreg\Economy\TableOfDebits;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\ChartOfTemplates;
use itbz\stb\Accounting\Template;
use itbz\stb\Utils\Amount;
use itbz\utils\Serializer;
use itbz\utils\Exception\SerializeException;
use itbz\datamapper\ModelInterface;
use itbz\datamapper\pdo\Mapper;
use itbz\datamapper\pdo\table\Table;
use itbz\datamapper\exception\DataNotFoundException;
use PDOStatement;

/**
 * Maps Accountant objects to database rows
 *
 * @package mreg\Mapper
 */
class AccountantMapper extends Mapper
{

    /**
     * Maps Accountant objects to database rows
     *
     * @param Table $table
     * @param Accountant $prototype
     */
    public function __construct(Table $table, Accountant $prototype)
    {
        parent::__construct($table, $prototype);
    }


    /**
     * Find accountant based on id
     *
     * Recursively finds parents and builds the debits object
     *
     * @param string $id
     *
     * @return Accountant
     */
    public function findByPk($id)
    {
        $row = parent::findByPk($id);

        try {
            $accounts = Serializer::unserialize($row['accounts']);
            $channels = Serializer::unserialize($row['channels']);
            $templates = Serializer::unserialize($row['templates']);
            $debits = Serializer::unserialize($row['debits']);
        } catch (SerializeException $e) {
            $msg = "Unable to unserialize accountant id '$id'";
            throw new Exception($msg, 0, $e);
        }

        // Recursively get parent to build debits object
        if ( $row['id'] != $row['parent'] ) {
            try {
                $parent = $this->findByPk($row['parent']);
                $debits->setParent($parent->getDebits());
            } catch (DataNotFoundException $e) {
                $pid = $row['parent'];
                $id = $row['id'];
                $msg = "Parent accountant '$pid' for '$id' does not exist";
                throw new Exception($msg, 1, $e);
            }
        }
        
        // Build accountant from row
        $accountant = parent::getNewModel();
        $accountant->setId($row['id']);
        $accountant->setParentId($row['parent']);
        $accountant->setAccounts($accounts);
        $accountant->setChannels($channels);
        $accountant->setTemplates($templates);
        $accountant->setDebits($debits);

        return $accountant;
    }


    /**
     * Extract data from accountant
     *
     * @param ModelInterface $accountant Accountant object
     * @param int $context Extract context ignored
     * @param array $use List of properties to extract
     *
     * @return ExpressionSet
     */
    protected function extractArray(
        ModelInterface $accountant,
        $context,
        array $use = NULL
    )
    {
        $data = array(
            'id' => $accountant->getId(),
            'accounts' => Serializer::serialize($accountant->getAccounts()),
            'channels' => Serializer::serialize($accountant->getChannels()),
            'templates' => Serializer::serialize($accountant->getTemplates()),
            'debits' => Serializer::serialize($accountant->getDebits()),
            'parent' => $accountant->getParentId()
        );
        
        if (is_null($data['id'])) {
            unset($data['id']);
        }

        if (is_null($data['parent'])) {
            unset($data['parent']);
        }
        
        // Only return data with keys present in extract (case insensitive)
        if ($use) {
            $data = array_intersect_ukey(
                $data,
                array_flip($use),
                'strcasecmp'
            );
        }
        
        return $data;
    }


    /**
     * Override standard DataMapper interator
     *
     * Tricks find into returning arrays instead of models
     *
     * @param PDOStatement $stmt
     *
     * @return \Iterator
     */
    protected function getIterator(PDOStatement $stmt)
    {
        return $stmt;
    }

}