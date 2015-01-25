<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\stb\Accounting\Formatter\SIE;
use itbz\stb\Utils\Amount;
use mreg\Exception\EconomyException;
use DateTime;
use DateInterval;
use mreg\Model\Dir\Faction;
use mreg\Model\Dir\Member;
use mreg\Mapper\XrefMapper;
use mreg\Mapper\DirMapper;
use itbz\datamapper\pdo\Search;
use itbz\datamapper\pdo\Expression;
use itbz\datamapper\pdo\access\AcMapper;
use itbz\datamapper\exception\DataNotFoundException;

/**
 * Mreg invoice ledger
 *
 * @package mreg\Economy
 */
class Ledger
{

    /**
     * Accountant managing ledger
     *
     * @var Accountant
     */
    private $_accountant;


    /**
     * Faction accountant is working for
     *
     * @var Faction
     */
    private $_faction;


    /**
     * Mapper to find members for faction
     *
     * @var XrefMapper
     */
    private $_factionToMemberMapper;


    /**
     * Mapper to update members
     *
     * @var DirMapper
     */
    private $_memberMapper;


    /**
     * Invoice mapper
     *
     * @var AcMapper
     */
    private $_invoiceMapper;


    /**
     * SIE object for exporting to bookkeeping
     *
     * @var SIE
     */
    private $_sie;


    /**
     * Mreg invoice ledger
     *
     * @param Accountant $accountant Accountant managing ledger
     *
     * @param Faction $faction Faction accountant is working for
     *
     * @param XrefMapper $factionToMemberMapper
     *
     * @param DirMapper $memberMapper
     *
     * @param AcMapper $invoiceMapper
     *
     * @param SIE $sie
     *
     * @throws EconomyException if accountant is not valid
     */
    public function __construct(
        Accountant $accountant,
        Faction $faction,
        XrefMapper $factionToMemberMapper,
        DirMapper $memberMapper,
        AcMapper $invoiceMapper,
        SIE $sie
    )
    {
        if (!$accountant->isValid($msg)) {
            throw new EconomyException($msg);
        }
        $this->_accountant = $accountant;
        $this->_faction = $faction;
        $this->_factionToMemberMapper = $factionToMemberMapper;
        $this->_memberMapper = $memberMapper;
        $this->_invoiceMapper = $invoiceMapper;
        $this->_sie = $sie;
    }


    /**
     * Get accountant managing ledger
     *
     * @return Accountant
     */
    public function getAccountant()
    {
        return $this->_accountant;
    }


    /**
     * Get faction accountant is working for
     *
     * @return Faction
     */
    public function getFaction()
    {
        return $this->_faction;
    }


    /**
     * Bill all active members of loaded faction
     *
     * @param string $spec Specification text
     *
     * @param DateTime $date Billing date
     *
     * @param DateInterval $interval Invoice due interval
     *
     * @return int Number of invoices created
     */
    public function billAllMembers(
        $spec = '',
        DateTime $date = NULL,
        DateInterval $interval = NULL
    )
    {
        assert('is_string($spec)');

        $xrefs = $this->_factionToMemberMapper->findMany(
            array(
                'master_id' => $this->_faction->getId(),
                'state' => 'OK'
            ),
            new Search()
        );
        
        $count = 0;
        foreach ($xrefs as $xref) {
            $this->billMember(
                $this->_memberMapper->findByPk($xref->getForeignId()),
                $spec,
                $date,
                $interval
            );
            $count++;
        }
        
        return $count;
    }


    /**
     * Bill member according to registered payment class
     *
     * @param Member $member
     *
     * @param string $spec Specification text
     *
     * @param DateTime $date Billing date
     *
     * @param DateInterval $interval Invoice due interval
     *
     * @return void
     */
    function billMember(
        Member $member, 
        $spec = '',
        DateTime $date = NULL,
        DateInterval $interval = NULL
    )
    {
        assert('is_string($spec)');

        $debitClass = $this->getDebitClass($member);

        // Update member debit class
        if ($debitClass->getName() !== $member->getDebitClass()) {
            $member->setDebitClass($debitClass->getName());
            $this->_memberMapper->save($member);
        }

        $invoice = $this->_invoiceMapper->getNewModel();

        if ($date) {
            $invoice->setCreated($date);
        }
        
        if ($interval) {
            $invoice->setInterval($interval);
        }

        if ($member->getPaymentType() === 'AG') {
            $invoice->setAutogiro();
        }

        // Set template if this is not a blanco invoice
        if (!$debitClass instanceof BlancoClass) {
            $spec .= ' (avgiftsklass ' . $debitClass->getName() .')';
            $invoice->setTemplate(
                $this->getPreparedTemplate($debitClass, $member->getId())
            );
        }

        $invoice->setAmount($debitClass->getCharge());
        $invoice->setDescription($spec);
        $invoice->setRecipientId($this->_faction->getId());
        $invoice->setPayerId($member->getId());
        $invoice->setTitle(
            sprintf('Faktura %s %s', $member->getNames(), $member->getSurname())
        );
        
        $this->insertInvoice($invoice);
    }


    /**
     * Invoice callback for generating claim invoices
     *
     * Should not be called directly
     *
     * @param AbstractInvoice $invoker
     *
     * @param Amount $claim
     *
     * @return void
     */
    public function memberClaimCallback(AbstractInvoice $invoker, Amount $claim)
    {
        $templates = $this->_accountant->getTemplates();
        $template = $templates->getTemplate('MSKULD');
        $template->substitute(
            array(
                'AVGIFT' => $claim,
                'M-NR' => $invoker->getPayerId()
            )
        );
        
        $invoice = $this->_invoiceMapper->getNewModel();
        $invoice->setRecipientId($invoker->getRecipientId());
        $invoice->setPayerId($invoker->getPayerId());
        
        if ($invoker->isAutogiro()) {
            $invoice->setAutogiro();
        }
        
        $invoice->setAmount($claim);
        $invoice->setTemplate($template);
        $invoice->setDescription('Skuld för ' . $invoker->getDescription());
        $invoice->setTitle($invoker->getTitle());
   
        $this->insertInvoice($invoice);
    }


    /**
     * Get debit class for member
     *
     * Get class from salary if salary is not 0. Else get saved value. If there
     * is no saved value return the lowest class.
     *
     * @param Member $member
     *
     * @return DebitClass
     *
     * @throws EconomyException if stored debit class is invalid
     */
    public function getDebitClass(Member $member)
    {
        $debits = $this->_accountant->getDebits();
        
        // Get debit class from salary if salary is set
        $salary = $member->getSalary();
        if (!$salary->equals(new Amount('0'))) {

            return $debits->getClassFromSalary($salary);
        }

        // Try to get class from debit class name if set
        $debitClass = $member->getDebitClass();
        if ($debitClass) {
            try {

                return $debits->getClass($debitClass);
            } catch (EconomyException $e) {
                $msg = "Felaktig avgiftsklass '{$debitClass}'";
                $msg .= " för medlem '{$member->getId()}'";
                throw new EconomyException($msg, 0, $e);
            }
        }
        
        // No class found
        return new BlancoClass();
    }


    /**
     * Pay invoice using channel 'PG'
     *
     * @param AbstractInvoice $invoice
     *
     * @param Amount $amount
     *
     * @param bool $billClaim TRUE if claim invoice should be created
     *
     * @param DateTime $date Date when payment occorred
     *
     * @return void
     */
    public function payPg(
        AbstractInvoice $invoice,
        Amount $amount,
        $billClaim = TRUE,
        DateTime $date = NULL
    )
    {
        $this->pay('PG', $invoice, $amount, $billClaim, $date);
    }


    /**
     * Pay invoice using channel 'BG'
     *
     * @param AbstractInvoice $invoice
     *
     * @param Amount $amount
     *
     * @param bool $billClaim TRUE if claim invoice should be created
     *
     * @param DateTime $date Date when payment occorred
     *
     * @return void
     */
    public function payBg(
        AbstractInvoice $invoice,
        Amount $amount,
        $billClaim = TRUE,
        DateTime $date = NULL
    )
    {
        $this->pay('BG', $invoice, $amount, $billClaim, $date);
    }


    /**
     * Pay invoice using channel 'AG'
     *
     * @param AbstractInvoice $invoice
     *
     * @param Amount $amount
     *
     * @param bool $billClaim TRUE if claim invoice should be created
     *
     * @param DateTime $date Date when payment occorred
     *
     * @return void
     */
    public function payAg(
        AbstractInvoice $invoice,
        Amount $amount,
        $billClaim = TRUE,
        DateTime $date = NULL
    )
    {
        $this->pay('AG', $invoice, $amount, $billClaim, $date);
    }


    /**
     * Pay invoice using channel 'K'
     *
     * @param AbstractInvoice $invoice
     *
     * @param Amount $amount
     *
     * @param bool $billClaim TRUE if claim invoice should be created
     *
     * @param DateTime $date Date when payment occorred
     *
     * @return void
     */
    public function payK(
        AbstractInvoice $invoice,
        Amount $amount,
        $billClaim = TRUE,
        DateTime $date = NULL
    )
    {
        $this->pay('K', $invoice, $amount, $billClaim, $date);
    }


    /**
     * Export payed invoices
     *
     * Optionally specify start and end date of accounting year. To have affect
     * both values must be specified. If omitted accounting year is set to
     * curent year, starting on the first of janurary and ending on the last
     * of december.
     *
     * @param DateTime $from Range start
     *
     * @param DateTime $to Range end
     *
     * @param bool $fresh TRUE if only non-exported invoices should be exported
     *
     * @param DateTime $yearStart
     *
     * @param DateTime $yearEnd
     *
     * @return string
     *
     * @throws EconomyException if from represents an earlier date than
     * yearStart
     *
     * @throws EconomyException if to represents a later date than yearEnd
     */
    public function export(
        DateTime $from,
        DateTime $to,
        $fresh = TRUE,
        DateTime $yearStart = NULL,
        DateTime $yearEnd = NULL
    )
    {
        assert('is_bool($fresh)');

        $this->_sie->clear();
        $accounts = $this->_accountant->getAccounts();
        $this->_sie->setTypeOfChart($accounts->getChartType());

        if (!$yearStart || !$yearEnd) {
            $year = date('Y');
            $yearStart = new DateTime("$year-01-01");
            $yearEnd = new DateTime("$year-12-31");
        }

        // Only the date part is of interest
        $from->setTime(0, 0, 0);
        $to->setTime(0, 0, 0);
        $yearStart->setTime(0, 0, 0);
        $yearEnd->setTime(0, 0, 0);

        if ($from < $yearStart) {
            $msg = "Kan ej exportera fakturor från föregående bokföringsår";
            throw new EconomyException($msg);
        }

        if ($to > $yearEnd) {
            $msg = "Kan ej exportera fakturor från nästa bokföringsår";
            throw new EconomyException($msg);
        }

        $this->_sie->setYear($yearStart, $yearEnd);

        $conditions = array(
            new Expression('paidVia', '', '!='),
            new Expression('tPaid', $from->getTimestamp(), '>')
        );
        
        if ($fresh) {
            $conditions['tExported'] = 0;
        }

        $iter = $this->findInvoices($conditions);

        foreach ($iter as $invoice) {
            if ($invoice->getPaidDate() <= $to) {
                $this->_sie->addVerification($invoice->exportToAccounting());
                $this->saveInvoice($invoice);
            }
        }

        return $this->_sie->generate();
    }


    /**
     * Find invoices for this accountant
     *
     * @param array $conditions
     *
     * @param Search $search
     *
     * @return \Iterator
     */
    public function findInvoices(array $conditions, Search $search = NULL)
    {
        $conditions['recipientId'] = $this->_faction->getId();
        if (!$search) {
            $search = new Search();
        }

        return $this->_invoiceMapper->findMany($conditions, $search);
    }

    
    /**
     * Save invoice
     *
     * @param AbstractInvoice $invoice
     *
     * @return int Number of rows altered in db
     */
    public function saveInvoice(AbstractInvoice $invoice)
    {
        return $this->_invoiceMapper->save($invoice);
    }


    /**
     * Get template for debit class with basic substitutions made
     *
     * @param DebitClass $debitClass
     *
     * @param string $payerId
     *
     * @return Template
     */
    private function getPreparedTemplate(DebitClass $debitClass, $payerId)
    {
        assert('is_string($payerId)');
        $channels = $this->_accountant->getChannels();

        // Get bookkeeping account for syndikat
        $member = $this->_memberMapper->getNewModel();
        $member->setId($payerId);
        $iter = $this->_factionToMemberMapper->findXrefMasters(
            $member,
            new Search(),
            new Expression('state', 'OK')
        );
        $syndName = 'syndikatlösa';
        foreach ($iter as $relation) {
            list(, $faction) = $relation;
            if ($faction->getType() == 'type_faction_SYNDIKAT') {
                $syndName = $faction->getName();
                break;
            }
        }
        $syndAccount = $channels->getAccount($syndName);

        // Get bookkeeping account for income
        $incomeAccount = $channels->getAccount($debitClass->getName());

        $template = $this->_accountant->getTemplates()->getTemplate(
            $debitClass->getTemplateName()
        );

        $template->substitute($debitClass->getDebits());
        $template->substitute(
            array(
                'M-NR' => $payerId,
                'SYNDIKATKANAL' => $syndAccount->getNumber(),
                'AVGIFTSKANAL' => $incomeAccount->getNumber()
            )
        );
        
        return $template;
    }


    /**
     * Pay invoice
     *
     * @param string $channel Name of payment channel used
     *
     * @param AbstractInvoice $invoice
     *
     * @param Amount $amount
     *
     * @param bool $billClaim TRUE if claim invoice should be created
     *
     * @param DateTime $date Date when payment occorred
     *
     * @return void
     *
     * @throws EconomyException if invoice does not exist
     *
     * @throws EconomyException if invoice is already payed
     */
    private function pay(
        $channel,
        AbstractInvoice $invoice,
        Amount $amount,
        $billClaim = TRUE,
        DateTime $date = NULL
    )
    {
        assert('is_string($channel)');
        assert('is_bool($billClaim)');

        if ($invoice->isPaid()) {
            $msg = "Faktura '{$invoice->getId()}' är redan betalad";
            throw new EconomyException($msg);
        }
        
        // Get template for payed amount if blanco
        if ($invoice->isBlanco()) {
            $debits = $this->_accountant->getDebits();
            $debitClass = $debits->getClassFromAmount($amount);
            $invoice->setTemplate(
                $this->getPreparedTemplate($debitClass, $invoice->getPayerId())
            );
            $invoice->setAmount($debitClass->getCharge());
        }
        
        if ($billClaim) {
            $claimCallback = array($this, 'memberClaimCallback');
        } else {
            $claimCallback = NULL;
        }
        
        $invoice->pay(
            $channel,
            $amount,
            $this->_accountant->getChannels()->getAccount($channel),
            $this->_accountant->getAccounts(),
            $date,
            $claimCallback
        );

        $this->saveInvoice($invoice);
    }


    /**
     * Save invoice and calculate OCR on generated id
     *
     * @param AbstractInvoice $invoice
     *
     * @return void
     */
    private function insertInvoice(AbstractInvoice $invoice)
    {
        $this->saveInvoice($invoice);
        $id = $this->_invoiceMapper->getLastInsertId();
        $invoice->setId((string)$id);
        $invoice->createOCR();
        $this->saveInvoice($invoice);
    }

}
