<?php
namespace mreg\Mapper;

use itbz\datamapper\pdo\table\SqliteTable;
use mreg\Economy\Accountant;
use itbz\stb\Accounting\Formatter\SIE;
use itbz\utils\Serializer;
use itbz\stb\Accounting\Account;
use mreg\Wrappers\DebitsWrapper;
use mreg\Wrappers\AccountantWrapper;

class AccountantMapperTest extends \mreg\tests\Economy\AbstractEconomyTestCase
{

    function getPdo()
    {
        $accountant = $this->getValidAccountant();
        $accounts = Serializer::serialize($accountant->getAccounts());
        $channels = Serializer::serialize($accountant->getChannels());
        $templates = Serializer::serialize($accountant->getTemplates());
        $debits = Serializer::serialize($accountant->getDebits());

        $parentDebits = $this->getValidDebitsWithDeposition();
        $parentDebits = Serializer::serialize($parentDebits);

        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->query("CREATE TABLE accountants(id INTEGER, accounts, channels, templates, debits, parent DEFAULT '1', PRIMARY KEY(id ASC));");
        $pdo->query("INSERT INTO accountants VALUES (1, '$accounts', '$channels', '$templates', '$parentDebits', 1)");
        $pdo->query("INSERT INTO accountants VALUES (2, '$accounts', '$channels', '$templates', '$debits', 1)");
        $pdo->query("INSERT INTO accountants VALUES (3, 'invalid', 'invalid', 'invalid', 'invalid', 2)");
        $pdo->query("INSERT INTO accountants VALUES (4, '$accounts', '$channels', '$templates', '$debits', 5)"); // invalid parent!

        return  $pdo;
    }

    
    function testFindByPk()
    {
        $table = new SqliteTable('accountants', $this->getPdo());
        $prototype = new Accountant(new SIE());
        $mapper = new AccountantMapper($table, $prototype);
        
        $ac = $mapper->findByPk(2);
        $this->assertInstanceOf('\mreg\Economy\Accountant', $ac);

        // Validate that parent debits data is set
        $debits = $ac->getDebits();
        $parent = $debits->getParent();
        $this->assertEquals(array('PARENT'), $parent->getDebitNames());
    }


    /**
     * @expectedException mreg\Exception
     */
    function testFindInvalidParentError()
    {
        $table = new SqliteTable('accountants', $this->getPdo());
        $prototype = new Accountant(new SIE());
        $mapper = new AccountantMapper($table, $prototype);
        
        // id 4 has an invalid parent
        $ac = $mapper->findByPk(4);
    }


    /**
     * @expectedException mreg\Exception
     */
    function testFindByPkUnserializeError()
    {
        $table = new SqliteTable('accountants', $this->getPdo());
        $prototype = new Accountant(new SIE());
        $mapper = new AccountantMapper($table, $prototype);

        // Values in database for id == 3 are invalid
        $ac = $mapper->findByPk(3);
    }


    function testSave()
    {
        $table = new SqliteTable('accountants', $this->getPdo());
        $prototype = new Accountant(new SIE());
        
        $mapper = new AccountantMapper($table, $prototype);

        $accountant = new Accountant(new SIE());
        $mapper->save($accountant);
        
        // Id 5 will only exist if accountant was inserted
        $ac = $mapper->findByPk('5');
        $this->assertEquals('5', $ac->getId());
    }

}