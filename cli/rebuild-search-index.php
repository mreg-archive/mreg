#!/usr/bin/php
<?php
/**
 * Rebuild MyISAM fulltext indexed search table
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Cli
 */

use itbz\datamapper\pdo\Search;

$c = include "bootstrap.php";

$pdo                    = $c['pdo'];
$factionMapper          = $c['factionMapper'];
$memberMapper           = $c['memberMapper'];
$sysgroupMapper         = $c['systemGroupMapper'];
$userMapper             = $c['userMapper'];
$factionToFactionMapper = $c['factionFactionXrefMapper'];
$factionToMemberMapper  = $c['factionMemberXrefMapper'];
$memberInvoiceMapper    = $c['memberInvoiceMapper'];
$map                    = $c['routes'];

// Set up tables for rotation
$pdo->exec('DROP TABLE IF EXISTS `search__old`');
$pdo->exec('DROP TABLE IF EXISTS `search__new`');
$pdo->exec('CREATE TABLE `search__new` LIKE `search__active`');

// Disable indexing while inserting
$pdo->exec('ALTER TABLE `search__new` DISABLE KEYS');

// Create pdo insert statement
$insertStmt = $pdo->prepare(
    'INSERT INTO `search__new`
    (`uri`, `title`, `id`, `type`, `description`, `misc`)
    VALUES (? , ? , ? , ? , ? , ?)'
);

// Set authenticated user
$factionMapper->setAuthUser($c['user']);
$memberMapper->setAuthUser($c['user']);
$sysgroupMapper->setUser($c['user']->getName(), $c['user']->getGroups());
$userMapper->setUser($c['user']->getName(), $c['user']->getGroups());
$memberInvoiceMapper->setUser($c['user']->getName(), $c['user']->getGroups());

// Inserting data takes time..
set_time_limit(0);

// Insert data
indexFactions();
indexMembers();
indexSysgroups();
indexUsers();
indexMemberInvoices();

// Enable fulltext index
$pdo->exec('ALTER TABLE `search__new` ENABLE KEYS');

// Rotate tables
$pdo->exec(
    'RENAME TABLE
    `search__active` TO `search__old`,
    `search__new` TO `search__active`'
);

// Done
$c['logger']->addInfo('rebuild-search-index kördes utan problem');
exit(0);



/**
 * Write member invoices to serach table
 *
 * @return void
 */
function indexMemberInvoices()
{
    global $memberInvoiceMapper, $insertStmt, $map;

    $iterator = $memberInvoiceMapper->findMany(array(), new Search());
    foreach ($iterator as $invoice) {
        // Add searchable but hidden values
        $searchValues = array(
            'xref_member_' . $invoice->getPayerId(),
            'xref_faction_' . $invoice->getRecipientId(),
            'invoice_' . $invoice->getId(),
            'invoice_ocr_' . $invoice->getOCR()
        );

        if ($invoice->isPaid()) {
            $searchValues[] = 'invoice_paid_' . $invoice->getPaidVia();
        }
        if ($invoice->isPrinted()) {
            $searchValues[] = 'invoice_printed';
        }
        if ($invoice->isExpired()) {
            $searchValues[] = 'invoice_expired';
        }
        if ($invoice->isLocked()) {
            $searchValues[] = 'invoice_locked';
        }
        if ($invoice->isBlanco()) {
            $searchValues[] = 'invoice_blanco';
        }
        if ($invoice->isExported()) {
            $searchValues[] = 'invoice_exported';
        }
        if ($invoice->isAutogiro()) {
            $searchValues[] = 'invoice_ag';
        }

        // Create insert values
        $values = array(
            $map->generate(
                "member-invoices",
                array('id' => $invoice->getId())
            ),
            $invoice->getTitle(),
            $invoice->getId(),
            $invoice->getType(),
            $invoice->getDescription(),
            implode(' ', $searchValues)
        );
        
        // Execute insert statement
        $insertStmt->execute($values);
    }
}


/**
 * Write factions to serach table
 *
 * @return void
 */
function indexFactions()
{
    global $map, $factionMapper, $factionToFactionMapper,
        $factionToMemberMapper, $insertStmt;

    $iterator = $factionMapper->findMany(array(), new Search());
    foreach ($iterator as $faction) {
        // Add searchable but hidden values
        $searchValues = array(
            'id_' . $faction->getId(),
            $faction->getDescription(),
            $faction->getNotes()
        );

        // Add faction xrefs to searchable values
        $xrefs = $factionToFactionMapper->findMany(
            array('foreign_id' => $faction->getId()),
            new Search()
        );
        foreach ($xrefs as $xref) {
            $searchValues[] = sprintf(
                "xref_faction_%s_%s",
                $xref->getMasterId(),
                $xref->getState()
            );
        }

        // Add member xrefs to searchable values
        $xrefs = $factionToMemberMapper->findMany(
            array('master_id' => $faction->getId()),
            new Search()
        );
        $cActiveMembers = 0;  // Count the number of active members
        foreach ($xrefs as $xref) {
            $searchValues[] = sprintf(
                "xref_member_%s_%s",
                $xref->getForeignId(),
                $xref->getState()
            );
            if ($xref->getState() === 'OK') {
                $cActiveMembers++;
            }
        }

        // Create insert values
        $values = array(
            $map->generate(
                "factions.main",
                array('mainId' => $faction->getId())
            ),
            $faction->getTitle(),
            $faction->getId(),
            $faction->getType(),
            $faction->getDescription() . " ($cActiveMembers medlemmar)",
            implode(' ', $searchValues)
        );
        
        // Execute insert statement
        $insertStmt->execute($values);
    }
}


/**
 * Write members to serach table
 *
 * @return void
 */
function indexMembers()
{
    global $map, $memberMapper, $factionToMemberMapper, $insertStmt;

    $iterator = $memberMapper->findMany(array(), new Search());
    foreach ($iterator as $member) {
        // Add searchable but hidden values
        $searchValues = array(
            'personalid_' . $member->getPersonalId()->getId(),
            'id_' . $member->getId(),
            'sex_' . $member->getSex(),
            'workcondition_' . $member->getWorkCondition(),
            'debitclass_' . $member->getDebitClass(),
            'paymenttype_' . $member->getPaymentType(),
            'invoiceflag_' . $member->hasExpiredInvoices(),
            $member->getNotes()
        );

        // Add xrefs to searchable values
        $xrefs = $factionToMemberMapper->findMany(
            array('foreign_id' => $member->getId()),
            new Search()
        );
        foreach ($xrefs as $xref) {
            $searchValues[] = sprintf(
                "xref_faction_%s_%s",
                $xref->getMasterId(),
                $xref->getState()
            );
        }
        
        // Calculate member age
        $dob = $member->getDateOfBirth();
        $age = $dob->diff(new DateTime);
        
        // Create insert values
        $values = array(
            $map->generate("members.main", array('mainId' => $member->getId())),
            $member->getTitle(),
            $member->getId(),
            $member->getType(),
            $member->getCachedLsName() . $age->format(' (%y år)'),
            implode(' ', $searchValues)
        );
        
        // Execute insert statement
        $insertStmt->execute($values);
    }
}


/**
 * Write system groups to serach table
 *
 * @return void
 */
function indexSysgroups()
{
    global $map, $sysgroupMapper, $insertStmt;

    $iterator = $sysgroupMapper->findMany(array(), new Search());
    foreach ($iterator as $sysgroup) {
        // Add searchable but hidden values
        $searchValues = array(
            'id_' . $sysgroup->getName(),
            $sysgroup->getDescription()
        );
        
        // Create insert values
        $values = array(
            $map->generate("group", array('name' => $sysgroup->getName())),
            $sysgroup->getTitle(),
            $sysgroup->getName(),
            $sysgroup->getType(),
            $sysgroup->getDescription(),
            implode(' ', $searchValues)
        );
        
        // Execute insert statement
        $insertStmt->execute($values);
    }
}


/**
 * Write users to serach table
 *
 * @return void
 */
function indexUsers()
{
    global $map, $userMapper, $insertStmt;

    $iterator = $userMapper->findMany(array(), new Search());
    foreach ($iterator as $user) {
        // Add searchable but hidden values
        $searchValues = array(
            'id_' . $user->getName(),
            $user->getFullName(),
        );
        foreach ($user->getGroups() as $group) {
            $searchValues[] = 'xref_sysgroup_' . $group;
        }
        
        // Create insert values
        $values = array(
            $map->generate("user", array('name' => $user->getName())),
            $user->getTitle(),
            $user->getName(),
            $user->getType(),
            $user->getNotes(),
            implode(' ', $searchValues)
        );
        
        // Execute insert statement
        $insertStmt->execute($values);
    }
}

