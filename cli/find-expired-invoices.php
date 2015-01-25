#!/usr/bin/php
<?php
/**
 * Flag members with expired invoices
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Cli
 */

namespace mreg\Economy;

use DateTime;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\Template;
use itbz\stb\Accounting\ChartOfTemplates;
use itbz\stb\Utils\Amount;
use itbz\stb\Accounting\Formatter\SIE;
use itbz\utils\Serializer;
use itbz\datamapper\pdo\Search;

set_time_limit(0);

$c = include "bootstrap.php";

$memberMapper           = $c['memberMapper'];
$memberInvoiceMapper    = $c['memberInvoiceMapper'];
$user                   = $c['user'];

$memberMapper->setAuthUser($user);
$memberInvoiceMapper->setUser($user->getName(), $user->getGroups());

$members = $memberMapper->findMany(array(), new Search);

foreach ($members as $member) {
    $flag = FALSE;

    $invoices = $memberInvoiceMapper->findMany(
        array('payerId' => $member->getId()),
        new Search
    );

    foreach ($invoices as $invoice) {
        if ($invoice->isExpired()) {
            $flag = TRUE;
            break;
        }
    }

    if ($flag != $member->hasExpiredInvoices()) {
        $member->setInvoiceFlag($flag);
        $memberMapper->save($member);
    }
}

$c['logger']->addInfo('find-expired-invoices kördes utan problem');
