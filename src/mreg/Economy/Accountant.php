<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\ChartOfTemplates;
use itbz\stb\Accounting\Template;
use itbz\stb\Accounting\Formatter\VISMAkml;
use itbz\stb\Accounting\Formatter\SIE;
use mreg\Exception\EconomyException;
use Mreg\Member;

/**
 * Accounting manager
 *
 * An Accountant manages Channels, Charts of accounts, Templates and Tables of
 * debits for mreg enteties capable of creating invoices
 *
 * @package mreg\Economy
 */
class Accountant implements \itbz\datamapper\ModelInterface
{

    /**
     * Id of this accountant
     *
     * @var string
     */
    private $_id;


    /**
     * Id of parent of this accountant
     *
     * @var string
     */
    private $_parentId;


    /**
     * Chart of accounts
     *
     * @var ChartOfAccounts
     */
    private $_accounts;


    /**
     * Bookeeping channels
     *
     * @var Channels
     */
    private $_channels;


    /**
     * Chart of Templates
     *
     * @var ChartOfTemplates
     */
    private $_templates;


    /**
     * Table of debit classes
     *
     * @var TableOfDebits
     */
    private $_debits;


    /**
     * SIE object for exporting and importing account charts
     *
     * @var SIE
     */
    private $_sie;


    /**
     * List of channels that are required
     *
     * @var array
     */
    private static $_requiredChannels = array(
        'K', 'PG', 'BG', 'AG',
        'AA', 'A', 'B', 'C', 'D'
    );


    /**
     * List of templates that are required
     *
     * @var array
     */
    private static $_requiredTemplates = array(
        'MSKULD'
    );


    /**
     * Create empty objects in container
     *
     * @param SIE $sie
     */
    public function __construct(SIE $sie)
    {
        $this->_sie = $sie;
        $this->_accounts = new ChartOfAccounts();
        $this->_channels = new Channels();
        $this->_templates = new ChartOfTemplates;
        $this->_debits = new TableOfDebits();
    }


    /**
     * Required by ModelInterface
     *
     * Void because AccountantMapper writes directly to object
     *
     * @param array $data
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function load(array $data)
    {
    }


    /**
     * Required by ModelInterface
     *
     * Void because AccountantMapper reads directly from object
     *
     * @param int $context
     *
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
        return array();
    }


    /**
     * Set id of this accountant
     *
     * @param string $id A numerical string
     *
     * @return void
     */
    public function setId($id)
    {
        assert('is_numeric($id)');
        $this->_id = $id;
        if (is_null($this->getParentId())) {
            $this->setParentId($id);
        }
    }
    
    
    /**
     * Set id of parent of this accountant
     *
     * @param string $id A numerical string
     *
     * @return void
     */
    public function setParentId($id)
    {
        assert('is_numeric($id)');
        $this->_parentId = $id;
    }


    /**
     * Set chart of accounts
     *
     * @param ChartOfAccounts $accounts
     *
     * @return void
     */
    public function setAccounts(ChartOfAccounts $accounts)
    {
        $this->_accounts = $accounts;
    }


    /**
     * Set channels
     *
     * @param Channels $channels
     *
     * @return void
     */
    public function setChannels(Channels $channels)
    {
        $this->_channels = $channels;
    }


    /**
     * Set chart of templates
     *
     * @param ChartOfTemplates $templates
     *
     * @return void
     */
    public function setTemplates(ChartOfTemplates $templates)
    {
        $this->_templates = $templates;
    }


    /**
     * Set table of debit classes
     *
     * @param TableOfDebits $tableOfDebits
     *
     * @return void
     */
    public function setDebits(TableOfDebits $tableOfDebits)
    {
        $this->_debits = $tableOfDebits;
    }


    /**
     * Get id of this accountant
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Get id of parent of this accountant
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->_parentId;
    }


    /**
     * Get chart of accounts
     *
     * @return ChartOfAccounts
     */
    public function getAccounts()
    {
        return $this->_accounts;
    }


    /**
     * Get channels
     *
     * @return Channels
     */
    public function getChannels()
    {
        return $this->_channels;
    }


    /**
     * Get chart of templates
     *
     * @return ChartOfTemplates
     */
    public function getTemplates()
    {
        return $this->_templates;
    }


    /**
     * Get table of debit classes
     *
     * @return TableOfDebits
     */
    public function getDebits()
    {
        return $this->_debits;
    }


    /**
     * Validate internal consistency
     *
     * @param string $msg Will contain description on error
     *
     * @return bool
     */
    public function isValid(&$msg)
    {
        $msg = '';

        // Validate that channels exist in chart of accounts
        foreach ($this->_channels->getChannels() as $id => $account) {
            $number = $account->getNumber();
            // Account should exist in chart
            if (!$this->_accounts->accountExists($number)) {
                $msg = "Konto '$number' i kanal '$id' saknas i kontoplan";

                return FALSE;
            }
            // Account should equal account in chart
            $accountInChart = $this->_accounts->getAccount($number);
            if (!$accountInChart->equals($account)) {
                $msg = "Konto '$number' i kanal '$id' stämmer ej med kontoplan";

                return FALSE;
            }
        }

        // Validate required channels
        foreach (self::$_requiredChannels as $channelId) {
            if (!$this->_channels->exists($channelId)) {
                $msg = "Bokföringskanal '$channelId' saknas.";

                return FALSE;
            }
        }

        // Validate that template accounts exists in chart of accounts
        foreach ($this->_templates->getTemplates() as $id => $template) {
            foreach ($template->getTransactions() as $transactData) {
                $number = $transactData[0];
                if (
                    is_numeric($number)
                    && !$this->_accounts->accountExists($number)
                ) {
                    $msg = "Konto '$number' i mall '$id' saknas i kontoplan";

                    return FALSE;
                }
            }
        }

        // Validate that templates linked in TableOfDebits exists
        foreach ($this->_debits->getClasses() as $debitClass) {
            $tmplName = $debitClass->getTemplateName();
            if (!$this->_templates->exists($tmplName)) {
                $class = $debitClass->getName();
                $msg = "Mall '$tmplName' i avgiftsklass '$class' saknas";

                return FALSE;
            }
        }

        // Validate required templates
        foreach (self::$_requiredTemplates as $tmplName) {
            if (!$this->_templates->exists($tmplName)) {
                $msg = "Mall '$tmplName' saknas.";

                return FALSE;
            }
        }

        return TRUE;
    }


    /**
     * Export templates in VISMAkml format
     *
     * @return string
     */
    public function exportTemplates()
    {
        $kml = new VISMAkml();
        foreach ($this->_templates->getTemplates() as $template) {
            $kml->addTemplate($template);
        }

        return $kml->export();
    }


    /**
     * Import templates from VISMAkml format
     *
     * @param string $kmlstr
     *
     * @return void
     */
    public function importTemplates($kmlstr)
    {
        assert('is_string($kmlstr)');
        $kml = new VISMAkml();
        $kml->import($kmlstr);
        foreach ($kml->getTemplates() as $template) {
            $this->_templates->addTemplate($template);
        }
    }


    /**
     * Export accounts in SIE format
     *
     * @param string $name Descriptive name of chart of accounts
     *
     * @return string
     */
    public function exportAccounts($name = 'Kontoplan exporterad från mreg')
    {
        assert('is_string($name)');
        return $this->_sie->exportChart($name, $this->_accounts);
    }


    /**
     * Import accounts from SIE format
     *
     * Completely overwrites the current chart of accounts
     *
     * @param string $siestr
     *
     * @return void
     */
    public function importAccounts($siestr)
    {
        assert('is_string($siestr)');
        $this->_accounts = $this->_sie->importChart($siestr);
    }

}
