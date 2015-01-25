#!/usr/bin/php
<?php
/**
 * Populate the database with default accountants.
 *
 * Note that this should only be done after a database rebuild
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

$c = include "bootstrap.php";

$dir                = $c['config']['contentDir'];
$accountantMapper   = $c['accountantMapper'];
$factionMapper      = $c['factionMapper'];



/*
 * Write defaults to filesystem
 */
$ac = new Accountant(new SIE());
$ac->setAccounts(getDefaultChart());
$ac->setTemplates(getDefaultTemplates());
$ac->setChannels(getDefaultChannels(getDefaultChart()));
$ac->setDebits(getTableOfDebits());

if (!$ac->isValid($msg)) {
    throw new \mreg\Exception('Kan ej skapa standardbokhållare: ' . $msg);
}

file_put_contents(
    $dir . 'economy-data/Baskontoplan.kp',
    $ac->exportAccounts()
);

file_put_contents(
    $dir . 'economy-data/Baskontoplan.base64',
    Serializer::serialize($ac->getAccounts())
);

file_put_contents(
    $dir . 'economy-data/konteringsmallar.kml',
    $ac->exportTemplates()
);

file_put_contents(
    $dir . 'economy-data/konteringsmallar.base64',
    Serializer::serialize($ac->getTemplates())
);

file_put_contents(
    $dir . 'economy-data/kanaler-standard.base64',
    Serializer::serialize($ac->getChannels())
);

file_put_contents(
    $dir . 'economy-data/avgifter-standard.base64',
    Serializer::serialize(getLsDebits())
);


// Only write to db if argument create is used..
if (!isset($argv[1]) || $argv[1] != 'create') {
    die();
}

/*
 * Update accountant
 */
$ac->setId('1');
$ac->setParentId('1');
$accountantMapper->save($ac);



/*
 * Create accountants for distrikt
 */
$ac->setDebits(getDistriktDebits());

if (!$ac->isValid($msg)) {
    throw new \mreg\Exception('Kan ej skapa bokhållare för distrikt: ' . $msg);
}

$factionMapper->setAuthUser($c['user']);
$iter = $factionMapper->findMany(
    array('type' => 'DISTRIKT'),
    new Search
);

$sodraDistrikt = 1;
$nextId = 2;
foreach ($iter as $faction) {
    if ($faction->getName() == 'Södra Distriktet') {
        $sodraDistrikt = $nextId;
    }
    $ac->setId($nextId);
    $accountantMapper->save($ac);
    $faction->setAccountantId($nextId);
    $factionMapper->save($faction);
    $nextId++;
}



/*
 * Create accountant for Malmö
 */
$ac->setParentId($sodraDistrikt);
$ac->setId($nextId);
$ac->setChannels(
    appendMalmoChannels(
        getDefaultChart(),
        getDefaultChannels(getDefaultChart())
    )
);
$ac->setDebits(getMalmoDebits());

if (!$ac->isValid($msg)) {
    throw new \mreg\Exception('Kan ej skapa bokhållare för malmö: ' . $msg);
}

$accountantMapper->save($ac);
$malmols = $factionMapper->findByPk('396');
$malmols->setAccountantId($nextId);
$factionMapper->save($malmols);
$nextId++;



/*
 * Create accountant for Stockholm
 */
$ac->setParentId($sodraDistrikt);
$ac->setId($nextId);
$accounts = getDefaultChart();
$accounts->addAccount(new Account('2221', "S", 'Skuld för Vårdsyndikat'));
$accounts->addAccount(new Account('2222', "S", 'Skuld för Socialarbetarsyndikat'));
$ac->setAccounts($accounts);
$ac->setChannels(
    appendStockholmChannels(
        $accounts,
        getDefaultChannels($accounts)
    )
);
$ac->setDebits(getLsDebits());

if (!$ac->isValid($msg)) {
    throw new \mreg\Exception('Kan ej skapa bokhållare för stockholm: ' . $msg);
}

$accountantMapper->save($ac);
$sthlm = $factionMapper->findByPk('1');
$sthlm->setAccountantId($nextId);
$factionMapper->save($sthlm);
$nextId++;



/**
 * Get table of debits
 *
 * @return TableOfDebits
 */
function getTableOfDebits()
{
    $table = new TableOfDebits();
    $table->setClass('D', new Amount('0'), '', new Amount('0'), array());
    $table->setClass('C', new Amount('0'), '', new Amount('0'), array());
    $table->setClass('B', new Amount('50'), '', new Amount('6000'), array('CENTRAL'=>new Amount('50')));
    $table->setClass('A', new Amount('120'), '', new Amount('13000'), array('CENTRAL'=>new Amount('120')));
    $table->setClass('AA', new Amount('190'), '', new Amount('19000'), array('CENTRAL'=>new Amount('190')));
    return $table;
}


/**
 * Get standard distrikt table of debits
 *
 * @return TableOfDebits
 */
function getDistriktDebits()
{
    $table = new TableOfDebits();
    $table->setClass('D', new Amount('0'), '', new Amount('0'), array());
    $table->setClass('C', new Amount('0'), '', new Amount('0'), array());
    $table->setClass('B', new Amount('5'), '', new Amount('6000'), array('DISTRIKT'=>new Amount('5')));
    $table->setClass('A', new Amount('5'), '', new Amount('13000'), array('DISTRIKT'=>new Amount('5')));
    $table->setClass('AA', new Amount('5'), '', new Amount('19000'), array('DISTRIKT'=>new Amount('5')));
    return $table;
}


/**
 * Get standard table of debits
 *
 * @return TableOfDebits
 */
function getLsDebits()
{
    $table = new TableOfDebits();
    $debits = array(
        'SYNDIKAT' => new Amount('10'),
        'SF' => new Amount('5')
    );
    $table->setClass('D', new Amount('0'), 'MAVG', new Amount('0'), $debits);
    $table->setClass('C', new Amount('39'), 'MAVG', new Amount('0'), $debits);
    $table->setClass('B', new Amount('99'), 'MAVG', new Amount('6000'), $debits);
    $table->setClass('A', new Amount('199'), 'MAVG', new Amount('13000'), $debits);
    $table->setClass('AA', new Amount('299'), 'MAVG', new Amount('19000'), $debits);
    return $table;
}


/**
 * Get Malmö table of debits
 *
 * @return TableOfDebits
 */
function getMalmoDebits()
{
    $table = new TableOfDebits();
    $debits = array(
        'SYNDIKAT' => new Amount('10'),
        'SF' => new Amount('5')
    );
    $table->setClass('D', new Amount('69'), 'MAVG', new Amount('0'), $debits);
    $table->setClass('C', new Amount('39'), 'MAVG', new Amount('0'), $debits);
    $table->setClass('B', new Amount('99'), 'MAVG', new Amount('6000'), $debits);
    $table->setClass('A', new Amount('199'), 'MAVG', new Amount('13000'), $debits);
    $table->setClass('AA2', new Amount('299'), 'MAVG', new Amount('19000'), $debits);
    $table->setClass('AA1', new Amount('399'), 'MAVG', new Amount('24000'), $debits);
    return $table;
}        


/**
 * Append stockholm channels to collection
 *
 * @param ChartOfAccounts $accounts
 *
 * @param Channels $channels
 *
 * @return Channels
 */
function appendStockholmChannels(ChartOfAccounts $accounts, Channels $channels)
{
    // Not standard
    $channels->addChannel('Stockholms Vårdsyndikat', $accounts->getAccountFromName('Skuld för Vårdsyndikat'));
    $channels->addChannel('Stockholms Socialarbetarsyndikat', $accounts->getAccountFromName('Skuld för Socialarbetarsyndikat'));

    // Standard
    $channels->addChannel('Stockholms Utbildningssyndikat', $accounts->getAccountFromName('Skuld för Utbildningssyndikat'));
    $channels->addChannel('Stockholms Transportsyndikat', $accounts->getAccountFromName('Skuld för Transportsyndikat'));
    $channels->addChannel('Stockholms Handel- och Servicesyndikat', $accounts->getAccountFromName('Skuld för Handel- och Servicesyndikat'));
    return $channels;
}


/**
 * Append malmö channels to collection
 *
 * @param ChartOfAccounts $accounts
 *
 * @param Channels $channels
 *
 * @return Channels
 */
function appendMalmoChannels(ChartOfAccounts $accounts, Channels $channels)
{
    $channels->addChannel('Malmö Handel- och Servicesyndikat', $accounts->getAccountFromName('Skuld för Handel- och Servicesyndikat'));
    $channels->addChannel('Malmö Transportsyndikat', $accounts->getAccountFromName('Skuld för Transportsyndikat'));
    $channels->addChannel('Malmö Utbildningssyndikat', $accounts->getAccountFromName('Skuld för Utbildningssyndikat'));
    $channels->addChannel('Malmö Social- och Vårdsyndikat', $accounts->getAccountFromName('Skuld för Social- och Vårdsyndikat'));
    $channels->addChannel('Malmö Industrisyndikat', $accounts->getAccountFromName('Skuld för Industrisyndikat'));
    $channels->addChannel('Malmö Byggsyndikat', $accounts->getAccountFromName('Skuld för Byggsyndikat'));
    $channels->addChannel('Malmö Kultur- och Mediesyndikat', $accounts->getAccountFromName('Skuld för Kultur- och Mediesyndikat'));
    $channels->addChannel('Malmö Kommunal- och Statsanställdas syndikat', $accounts->getAccountFromName('Skuld för Kommunal- och Statsanställdas syndikat'));
    return $channels;
}


/**
 * Get default accounting channels
 *
 * @param ChartOfAccounts $accounts
 *
 * @return Channels
 */
function getDefaultChannels(ChartOfAccounts $accounts)
{
    $channels = new Channels();
    $channels->addChannel('K', $accounts->getAccountFromName('Kassa'));
    $channels->addChannel('PG', $accounts->getAccountFromName('PlusGiro'));
    $channels->addChannel('BG', $accounts->getAccountFromName('Bankgiro'));
    $channels->addChannel('AG', $accounts->getAccountFromName('Autogiro'));
    $channels->addChannel('syndikatlösa', $accounts->getAccountFromName('Skuld för syndikatlösa'));
    $channels->addChannel('AA', $accounts->getAccountFromName('AA-avgifter'));
    $channels->addChannel('AA1', $accounts->getAccountFromName('AA1-avgifter'));
    $channels->addChannel('AA2', $accounts->getAccountFromName('AA2-avgifter'));
    $channels->addChannel('AA3', $accounts->getAccountFromName('AA3-avgifter'));
    $channels->addChannel('AA4', $accounts->getAccountFromName('AA4-avgifter'));
    $channels->addChannel('AA5', $accounts->getAccountFromName('AA5-avgifter'));
    $channels->addChannel('AA6', $accounts->getAccountFromName('AA6-avgifter'));
    $channels->addChannel('AA7', $accounts->getAccountFromName('AA7-avgifter'));
    $channels->addChannel('A', $accounts->getAccountFromName('A-avgifter'));
    $channels->addChannel('A1', $accounts->getAccountFromName('A1-avgifter'));
    $channels->addChannel('A2', $accounts->getAccountFromName('A2-avgifter'));
    $channels->addChannel('A3', $accounts->getAccountFromName('A3-avgifter'));
    $channels->addChannel('A4', $accounts->getAccountFromName('A4-avgifter'));
    $channels->addChannel('A5', $accounts->getAccountFromName('A5-avgifter'));
    $channels->addChannel('A6', $accounts->getAccountFromName('A6-avgifter'));
    $channels->addChannel('A7', $accounts->getAccountFromName('A7-avgifter'));
    $channels->addChannel('B', $accounts->getAccountFromName('B-avgifter'));
    $channels->addChannel('B1', $accounts->getAccountFromName('B1-avgifter'));
    $channels->addChannel('B2', $accounts->getAccountFromName('B2-avgifter'));
    $channels->addChannel('B3', $accounts->getAccountFromName('B3-avgifter'));
    $channels->addChannel('B4', $accounts->getAccountFromName('B4-avgifter'));
    $channels->addChannel('B5', $accounts->getAccountFromName('B5-avgifter'));
    $channels->addChannel('B6', $accounts->getAccountFromName('B6-avgifter'));
    $channels->addChannel('B7', $accounts->getAccountFromName('B7-avgifter'));
    $channels->addChannel('C', $accounts->getAccountFromName('C-avgifter'));
    $channels->addChannel('C1', $accounts->getAccountFromName('C1-avgifter'));
    $channels->addChannel('C2', $accounts->getAccountFromName('C2-avgifter'));
    $channels->addChannel('C3', $accounts->getAccountFromName('C3-avgifter'));
    $channels->addChannel('C4', $accounts->getAccountFromName('C4-avgifter'));
    $channels->addChannel('C5', $accounts->getAccountFromName('C5-avgifter'));
    $channels->addChannel('C6', $accounts->getAccountFromName('C6-avgifter'));
    $channels->addChannel('C7', $accounts->getAccountFromName('C7-avgifter'));
    $channels->addChannel('D', $accounts->getAccountFromName('D-avgifter'));
    $channels->addChannel('D1', $accounts->getAccountFromName('D1-avgifter'));
    $channels->addChannel('D2', $accounts->getAccountFromName('D2-avgifter'));
    $channels->addChannel('D3', $accounts->getAccountFromName('D3-avgifter'));
    $channels->addChannel('D4', $accounts->getAccountFromName('D4-avgifter'));
    $channels->addChannel('D5', $accounts->getAccountFromName('D5-avgifter'));
    $channels->addChannel('D6', $accounts->getAccountFromName('D6-avgifter'));
    $channels->addChannel('D7', $accounts->getAccountFromName('D7-avgifter'));
    return $channels;
}


/**
 * Get default accounting templates
 *
 * @return ChartOfTemplates
 */
function getDefaultTemplates()
{
    $mavg = new Template();
    $mavg->setId('MAVG');
    $mavg->setName('Bet. medlemsavgift');
    $mavg->setText('Medl.fakt. {F-NR} [M:{M-NR}] [OCR:{OCR}] {SPEC}');
    $mavg->addTransaction('{AVGIFTSKANAL}', '-{AVGIFT}');
    $mavg->addTransaction('4110', '{CENTRAL}');
    $mavg->addTransaction('2421', '-{CENTRAL}');
    $mavg->addTransaction('4120', '{DISTRIKT}');
    $mavg->addTransaction('2422', '-{DISTRIKT}');
    $mavg->addTransaction('4130', '{SF}');
    $mavg->addTransaction('2071', '-{SF}');
    $mavg->addTransaction('4150', '{SYNDIKAT}');
    $mavg->addTransaction('{SYNDIKATKANAL}', '-{SYNDIKAT}');
    $mavg->addTransaction('{BETKANAL}', '{SUMMA}');
    $mavg->addTransaction('3990', '-{REST}');
    $mavg->addTransaction('1510', '{FORDRAN}');

    $mskuld = new Template();
    $mskuld->setId('MSKULD');
    $mskuld->setName('Bet. medlemsfordran');
    $mskuld->setText('Inbetald medlemsfordring fakt. {F-NR} [M:{M-NR}] [OCR:{OCR}]');
    $mskuld->addTransaction('1510', '-{AVGIFT}');
    $mskuld->addTransaction('{BETKANAL}', '{SUMMA}');
    $mskuld->addTransaction('3990', '-{REST}');
    $mskuld->addTransaction('1510', '{FORDRAN}');

    // Local pays dept to central. For use in VISMA
    $central = new Template();
    $central->setId('CENTRAL');
    $central->setName('Bet. central skuld');
    $central->setText('Bet.fakt. {F-NR} till centralt');
    $central->addTransaction('2421', '{SUMMA}');
    $central->addTransaction('1920', '-{SUMMA}');

    // Dummy template
    $dummy = new Template();
    $dummy->setId('');
    $dummy->setName('Tom mall');
    $dummy->setText('Tom mall');

    $templates = new ChartOfTemplates();
    $templates->addTemplate($mavg);
    $templates->addTemplate($mskuld);
    $templates->addTemplate($central);
    $templates->addTemplate($dummy);
    return $templates;
}


/**
 * Get default chart of accounts
 *
 * @return ChartOfAccounts
 */
function getDefaultChart()
{
    $chart = new ChartOfAccounts();

    $chart->addAccount(new Account('1510', "T", 'Medlemsfordringar'));
    $chart->addAccount(new Account('1910', "T", 'Kassa'));
    $chart->addAccount(new Account('1920', "T", 'PlusGiro'));
    $chart->addAccount(new Account('1930', "T", 'Bankgiro'));
    $chart->addAccount(new Account('1935', "T", 'Autogiro'));

    $chart->addAccount(new Account('2071', "S", 'Stridsfond'));
    $chart->addAccount(new Account('2072', "S", 'Studiefond'));

    $chart->addAccount(new Account('2210', "S", 'Skuld för Utbildningssyndikat'));
    $chart->addAccount(new Account('2220', "S", 'Skuld för Social- och Vårdsyndikat'));
    $chart->addAccount(new Account('2230', "S", 'Skuld för Handel- och Servicesyndikat'));
    $chart->addAccount(new Account('2240', "S", 'Skuld för Industrisyndikat'));
    $chart->addAccount(new Account('2250', "S", 'Skuld för Transportsyndikat'));
    $chart->addAccount(new Account('2260', "S", 'Skuld för Byggsyndikat'));
    $chart->addAccount(new Account('2270', "S", 'Skuld för Kultur- och Mediesyndikat'));
    $chart->addAccount(new Account('2280', "S", 'Skuld för Kommunal- och Statsanställdas syndikat'));
    $chart->addAccount(new Account('2290', "S", 'Skuld för syndikatlösa'));

    $chart->addAccount(new Account('2421', "S", 'Skuld för centrala avgifter'));
    $chart->addAccount(new Account('2422', "S", 'Skuld för distriktsavgifter'));
    $chart->addAccount(new Account('2425', "S", 'Förinbetalda avgifter'));

    $chart->addAccount(new Account('3000', "I", 'AA-avgifter'));
    $chart->addAccount(new Account('3001', "I", 'AA1-avgifter'));
    $chart->addAccount(new Account('3002', "I", 'AA2-avgifter'));
    $chart->addAccount(new Account('3003', "I", 'AA3-avgifter'));
    $chart->addAccount(new Account('3004', "I", 'AA4-avgifter'));
    $chart->addAccount(new Account('3005', "I", 'AA5-avgifter'));
    $chart->addAccount(new Account('3006', "I", 'AA6-avgifter'));
    $chart->addAccount(new Account('3007', "I", 'AA7-avgifter'));
    $chart->addAccount(new Account('3100', "I", 'A-avgifter'));
    $chart->addAccount(new Account('3101', "I", 'A1-avgifter'));
    $chart->addAccount(new Account('3102', "I", 'A2-avgifter'));
    $chart->addAccount(new Account('3103', "I", 'A3-avgifter'));
    $chart->addAccount(new Account('3104', "I", 'A4-avgifter'));
    $chart->addAccount(new Account('3105', "I", 'A5-avgifter'));
    $chart->addAccount(new Account('3106', "I", 'A6-avgifter'));
    $chart->addAccount(new Account('3107', "I", 'A7-avgifter'));
    $chart->addAccount(new Account('3200', "I", 'B-avgifter'));
    $chart->addAccount(new Account('3201', "I", 'B1-avgifter'));
    $chart->addAccount(new Account('3202', "I", 'B2-avgifter'));
    $chart->addAccount(new Account('3203', "I", 'B3-avgifter'));
    $chart->addAccount(new Account('3204', "I", 'B4-avgifter'));
    $chart->addAccount(new Account('3205', "I", 'B5-avgifter'));
    $chart->addAccount(new Account('3206', "I", 'B6-avgifter'));
    $chart->addAccount(new Account('3207', "I", 'B7-avgifter'));
    $chart->addAccount(new Account('3300', "I", 'C-avgifter'));
    $chart->addAccount(new Account('3301', "I", 'C1-avgifter'));
    $chart->addAccount(new Account('3302', "I", 'C2-avgifter'));
    $chart->addAccount(new Account('3303', "I", 'C3-avgifter'));
    $chart->addAccount(new Account('3304', "I", 'C4-avgifter'));
    $chart->addAccount(new Account('3305', "I", 'C5-avgifter'));
    $chart->addAccount(new Account('3306', "I", 'C6-avgifter'));
    $chart->addAccount(new Account('3307', "I", 'C7-avgifter'));
    $chart->addAccount(new Account('3400', "I", 'D-avgifter'));
    $chart->addAccount(new Account('3401', "I", 'D1-avgifter'));
    $chart->addAccount(new Account('3402', "I", 'D2-avgifter'));
    $chart->addAccount(new Account('3403', "I", 'D3-avgifter'));
    $chart->addAccount(new Account('3404', "I", 'D4-avgifter'));
    $chart->addAccount(new Account('3405', "I", 'D5-avgifter'));
    $chart->addAccount(new Account('3406', "I", 'D6-avgifter'));
    $chart->addAccount(new Account('3407', "I", 'D7-avgifter'));
    $chart->addAccount(new Account('3990', "I", 'Gåvor'));

    $chart->addAccount(new Account('4110', "K", 'Centrala avgifter'));
    $chart->addAccount(new Account('4120', "K", 'Distriktsavgifter'));
    $chart->addAccount(new Account('4130', "K", 'Stridsförberedande kostnader'));
    $chart->addAccount(new Account('4140', "K", 'Studieförberedande kostnader'));
    $chart->addAccount(new Account('4150', "K", 'Syndikatavgifter'));

    return $chart;
}
