<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\stb\Accounting\Template;
use itbz\stb\Accounting\Verification;
use itbz\stb\Accounting\Account;
use itbz\stb\Accounting\ChartOfAccounts;
use itbz\stb\Utils\Amount;
use itbz\stb\Utils\OCR;
use itbz\stb\Exception\InvalidStructureException;
use itbz\stb\Exception\InvalidAccountException;
use mreg\Exception\EconomyException;
use DateTime;
use DateInterval;
use mreg\NullObject\NullDate;
use itbz\utils\Serializer;

/**
 * Mreg invoice class
 *
 * @package mreg\Economy
 */
abstract class AbstractInvoice extends \mreg\Model\AcModel
{

    /**
     * Invoice title
     *
     * @var string
     */
    private $_title = '';


    /**
     * Id of recipient
     *
     * @var string
     */
    private $_recipientId;


    /**
     * Id of payer
     *
     * @var string
     */
    private $_payerId;


    /**
     * Invoice OCR number
     *
     * @var OCR
     */
    private $_ocr;


    /**
     * Date when invoice expires
     *
     * @var DateTime
     */
    private $_dateExpire;


    /**
     * Date when invoice was printed
     *
     * @var DateTime
     */
    private $_datePrinted;


    /**
     * Date when invoice was paid
     *
     * @var DateTime
     */
    private $_datePaid;


    /**
     * Date when invoice was exported
     *
     * @var DateTime
     */
    private $_dateExported;


    /**
     * Invoice amount
     *
     * @var Amount
     */
    private $_amount;


    /**
     * Flag if autogiro is expected
     *
     * @var bool
     */
    private $_isAutogiro = FALSE;

    
    /**
     * Channel incoive was paid via
     *
     * @var string
     */
    private $_paidVia = '';


    /**
     * Flag if invoice is locked for editing
     *
     * @var bool
     */
    private $_locked = FALSE;


    /**
     * String describing invoice
     *
     * @var string
     */
    private $_desc = '';


    /**
     * Verification Template
     *
     * @var Template
     */
    private $_template;


    /**
     * Verification created from Template when invoice is paid
     *
     * @var Verification
     */
    private $_verification;


    /**
     * Load data from mapper into model
     *
     * @param array $data
     *
     * @return void
     */
    public function load(array $data)
    {
        parent::load($data);

        $this->setTitle($data['title']);
        $this->setRecipientId($data['recipientId']);
        $this->setPayerId($data['payerId']);
        $this->setOCR(new OCR($data['ocr']));
        $this->setExpire(new DateTime('@' . $data['tExpire']));
        $this->setAmount(new Amount($data['amount']));
        $this->setPaidVia($data['paidVia']);
        $this->setDescription($data['description']);

        if (ctype_digit($data['tPrinted']) && $data['tPrinted'] != '0') {
            $this->setPrinted(new DateTime('@' . $data['tPrinted']));
        }
        if (ctype_digit($data['tPaid']) && $data['tPaid'] != '0') {
            $this->setPaidDate(new DateTime('@' . $data['tPaid']));
        }
        if (ctype_digit($data['tExported']) && $data['tExported'] != '0') {
            $this->setExported(new DateTime('@' . $data['tExported']));
        }
        if ($data['isAutogiro']) {
            $this->setAutogiro();
        }
        if ($data['locked']) {
            $this->lock();
        }
        if ($data['template']) {
            $this->setTemplate(Serializer::unserialize($data['template']));
        }
        if ($data['verification']) {
            $this->setVerification(
                Serializer::unserialize($data['verification'])
            );
        }
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        // Export transactions if invoice is paid
        $trans = array();
        if ($this->isPaid()) {
            foreach ($this->getVerification()->getTransactions() as $t) {
                $key = sprintf(
                    '(%s) %s',
                    $t->getAccount()->getNumber(),
                    $t->getAccount()->getName()
                );
                $trans[$key] = $t->getAmount()->format();
            }
        }

        return parent::export() + array(
            'recipientId' => $this->getRecipientId(),
            'payerId' => $this->getPayerId(),
            'ocr' => (string)$this->getOCR(),
            'tBilled' => $this->getCreated()->format('Y-m-d'),
            'tExpire' => $this->getExpireDate()->format('Y-m-d'),
            'tPrinted' => $this->getPrintedDate()->format('Y-m-d'),
            'tPaid' => $this->getPaidDate()->format('Y-m-d'),
            'tExported' => $this->getExportedDate()->format('Y-m-d'),
            'amount' => $this->getAmount()->format(),
            'paidVia' => $this->getPaidVia(),
            'description' => $this->getDescription(),
            'isBlanco' => $this->isBlanco(),
            'isAutogiro' => $this->isAutogiro(),
            'isPaid' => $this->isPaid(),
            'isLocked' => $this->isLocked(),
            'isExpired' => $this->isExpired(),
            'isPrinted' => $this->isPrinted(),
            'isExported' => $this->isExported(),
            'transactions' => $trans
        );
    }


    /**
     * Extract data for datastore
     *
     * @param int $context
     * @param array $using
     *
     * @return array
     */
    public function extract($context, array $using)
    {
        $template = $this->getTemplate();
        if ($template) {
            $template = Serializer::serialize($template);
        }
        $verification = NULL;
        if ($this->isPaid()) {
            $verification = Serializer::serialize($this->getVerification());
        }

        return parent::extract($context, $using) + array(
            'title' => $this->getTitle(),
            'recipientId' => $this->getRecipientId(),
            'payerId' => $this->getPayerId(),
            'ocr' => $this->getOCR(),
            'tExpire' => $this->getExpireDate()->getTimestamp(),
            'tPrinted' => $this->getPrintedDate()->getTimestamp(),
            'tPaid' => $this->getPaidDate()->getTimestamp(),
            'tExported' => $this->getExportedDate()->getTimestamp(),
            'amount' => $this->getAmount(),
            'isAutogiro' => $this->isAutogiro() ? '1' : '0',
            'paidVia' => $this->getPaidVia(),
            'locked' => $this->isLocked() ? '1' : '0',
            'description' => $this->getDescription(),
            'template' => $template,
            'verification' => $verification
        );
    }


    /**
     * Set invoice title
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        assert('is_string($title)');
        $this->_title = $title;
    }


    /**
     * Get invoice title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }


    /**
     * Set id of recipient
     *
     * @param string $recipientId
     *
     * @return void
     */
    public function setRecipientId($recipientId)
    {
        assert('is_string($recipientId)');
        $this->_recipientId = $recipientId;
    }


    /**
     * Get id of recipient
     *
     * @return string
     */
    public function getRecipientId()
    {
        return $this->_recipientId;
    }


    /**
     * Set id of payer
     *
     * @param string $payerId
     *
     * @return void
     */
    public function setPayerId($payerId)
    {
        assert('is_string($payerId)');
        $this->_payerId = $payerId;
    }


    /**
     * Get id of payer
     *
     * @return string
     */
    public function getPayerId()
    {
        return $this->_payerId;
    }


    /**
     * Set invoice OCR number
     *
     * @param OCR $ocr
     */
    public function setOCR(OCR $ocr)
    {
        $this->_ocr = $ocr;
    }


    /**
     * Get invoice OCR number
     *
     * @return OCR
     */
    public function getOCR()
    {
        if (!isset($this->_ocr)) {
            if ($this->getId()) {
                $this->createOCR();
            } else {
                $this->setOCR(new OCR);
            }
        }

        return $this->_ocr;
    }


    /**
     * Create OCR from invoice id
     *
     * @return void
     */
    public function createOCR()
    {
        $ocr = new OCR();
        $ocr->create($this->getId());
        $this->setOCR($ocr);
    }


    /**
     * Get date when invoice was created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        if (parent::getCreated() instanceof NullDate) {
            $this->setCreated(new DateTime());
        }
        
        return parent::getCreated();
    }


    /**
     * Set due interval
     *
     * Setting interval changes invoice expire date to created plus interval
     *
     * @param DateInterval $interval
     *
     * @return void
     */
    public function setInterval(DateInterval $interval)
    {
        $expire = clone $this->getCreated();
        $expire->setTime(23, 59, 59);
        $expire->add($interval);
        $this->setExpire($expire);
    }


    /**
     * Set date when invoice expires
     *
     * @param DateTime $expire
     *
     * @return void
     */
    public function setExpire(DateTime $expire)
    {
        $this->_dateExpire = $expire;
        $this->_dateExpire->setTimezone($this->getTimezone());
    }


    /**
     * Get date when invoice expires
     *
     * Defaults to 30 days
     *
     * @return DateTime
     */
    public function getExpireDate()
    {
        if (!isset($this->_dateExpire)) {
            $this->setInterval(new DateInterval('P30D'));       
        }

        return $this->_dateExpire;
    }


    /**
     * Check if invoice has expired
     *
     * @return bool
     */
    public function isExpired()
    {
        
        return !$this->isPaid() && new DateTime > $this->_dateExpire;
    }


    /**
     * Check if invoice has been printed
     *
     * @return bool
     */
    public function isPrinted()
    {
        return isset($this->_datePrinted);
    }


    /**
     * Set date when invoice was printed
     *
     * @param DateTime $date
     *
     * @return void
     */
    public function setPrinted(DateTime $date = NULL)
    {
        if (!$date) {
            $date = new DateTime();
        }
        $this->_datePrinted = $date;
        $this->_datePrinted->setTimezone($this->getTimezone());
    }


    /**
     * Get date when invoice was printed
     *
     * @return DateTime
     */
    public function getPrintedDate()
    {
        if (!isset($this->_datePrinted)) {
            return new NullDate();
        }

        return $this->_datePrinted;
    }


    /**
     * Set expected payment type to autogiro
     *
     * @return void
     */
    public function setAutogiro()
    {
        $this->_isAutogiro = TRUE;
    }


    /**
     * Check if expected payment type is autogiro
     *
     * @return bool
     */
    public function isAutogiro()
    {
        return $this->_isAutogiro;
    }


    /**
     * Lock invoice for editing
     *
     * @return void
     */
    public function lock()
    {
        $this->_locked = TRUE;
    }


    /**
     * Check if invoice is locked for edititng
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->_locked;
    }


    /**
     * Set invoice description
     *
     * @param string $desc
     *
     * @return void
     */
    public function setDescription($desc)
    {
        assert('is_string($desc)');
        $this->_desc = $desc;
    }

    
    /**
     * Get invoice description
     *
     * return string
     */
    public function getDescription()
    {
        return $this->_desc;
    }


    /**
     * Set invoice template
     *
     * @param Template $template
     *
     * @return void
     */
    public function setTemplate(Template $template)
    {
        $this->_template = $template;
    }


    /**
     * Get invoice template
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->_template;
    }


    /**
     * Set invoice amount
     *
     * @param Amount $amount
     *
     * @return void
     */
    public function setAmount(Amount $amount)
    {
        $this->_amount = $amount;
    }


    /**
     * Get invoice amount
     *
     * @return Amount
     */
    public function getAmount()
    {
        if (!isset($this->_amount)) {
            return new Amount('0');
        }
        
        return $this->_amount;
    }


    /**
     * Check if this is a blanco invoice
     *
     * @return bool
     */
    public function isBlanco()
    {
        return $this->getAmount()->equals(new Amount('0'));
    }


    /**
     * Set verification
     *
     * Must be a complete and balansed verification. Setting a verification
     * signifies marking the invoce as paid.
     *
     * @param Verification $verification
     *
     * @return void
     *
     * @throws EconomyException if $verification is not balanced
     */
    public function setVerification(Verification $verification)
    {
        if ( !$verification->isBalanced() ) {
            $msg = "Unable to save unbalanced verification";
            throw new EconomyException($msg);
        }
        $this->_verification = $verification;
    }


    /**
     * Get verification for paid invoice.
     *
     * @return Verification
     *
     * @throws EconomyException if invoice is not paid
     */
    public function getVerification()
    {
        if ( !$this->isPaid() ) {
            $msg = "Unable to get verification for unpaid invoice";
            throw new EconomyException($msg);
        }

        return $this->_verification;
    }


    /**
     * Check if invoice has been marked as paid
     *
     * @return bool
     */
    public function isPaid()
    {
        return isset($this->_verification);
    }


    /**
     * Get date when invoice was paid
     *
     * @return DateTime
     */
    public function getPaidDate()
    {
        if (!isset($this->_datePaid)) {
            return new NullDate();
        }

        return $this->_datePaid;
    }


    /**
     * Set channel invoice was paid via
     *
     * @param string $paidVia
     *
     * @return void
     */
    public function setPaidVia($paidVia)
    {
        assert('is_string($paidVia)');
        $this->_paidVia = $paidVia;
    }


    /**
     * Get channel invoice was paid via
     *
     * Empty string if invoice is not paid
     *
     * @return string
     */
    public function getPaidVia()
    {
        return $this->_paidVia;
    }


    /**
     * Pay invoice
     *
     * @param string $via
     * @param Amount $amount The amount actually paid
     * @param Account $paymentAccount
     * @param ChartOfAccounts $chart Chart used when converting template to
     * verification
     * @param DateTime $date Date when payment occured
     * @param callback $claimCallback Called if paying results in a claim on the
     * payer for the payee. Callback takes two arguments: this invoice and the
     * calculated claim.
     * @param callback $benefitCallback Called if paying results in a benefit to
     * the payee. Callback takes two arguments: this invoice and the calculated
     * benefit.
     *
     * @return Verification
     *
     * @throws EconomyException if template is not set
     * @throws EconomyException if any template term is not substituted
     * @throws EconomyException template account is not in ChartOfAccounts
     */
    public function pay(
        $via,
        Amount $amount,
        Account $paymentAccount,
        ChartOfAccounts $chart,
        DateTime $date = NULL,
        $claimCallback = NULL,
        $benefitCallback = NULL
    )
    {
        assert('is_string($via)');
        $this->setPaidVia($via);

        if (!isset($this->_template)) {
            $msg = "Unable to pay invoice without template";
            throw new EconomyException($msg);
        }
        
        if ( !$date ) {
            $date = new DateTime();
        }
        if ( !$claimCallback ) {
            $claimCallback = function() {
            };
        }
        if ( !$benefitCallback ) {
            $benefitCallback = function() {
            };
        }
        assert('is_callable($claimCallback)');
        assert('is_callable($benefitCallback)');

        // Calculate claim/benefit
        $claim = new Amount('0');
        $benefit = new Amount('0');
        $diff = clone $this->getAmount();
        $diff->subtract($amount);
        if ($diff->isGreaterThan($claim)) {
            $claim = $diff;
        } elseif ($diff->isLesserThan($benefit)) {
            $diff->invert();
            $benefit = $diff;
        }

        // Substitute template terms
        $this->_template->substitute(
            array(
                'BETKANAL' => $paymentAccount->getNumber(),
                'SUMMA' => $amount,
                'REST' => $benefit,
                'FORDRAN' => $claim,
                'F-NR' => $this->getId(),
                'OCR' => $this->getOCR(),
                'SPEC' => $this->getDescription(),
            )
        );

        // Create verification
        try {
            $ver = $this->_template->buildVerification($chart);
            $ver->setDate($date);
        } catch (InvalidStructureException $e) {
            $msg = "Verifikat kan ej skapas: " . $e->getMessage();
            throw new EconomyException($msg, 0, $e);
        } catch (InvalidAccountException $e) {
            $msg = "Verifikat kan ej skapas: " . $e->getMessage();
            throw new EconomyException($msg, 0, $e);
        }
        
        // Call claim/benefit callbacks
        if (!$claim->equals(new Amount('0'))) {
            call_user_func($claimCallback, $this, $claim);
        } elseif (!$benefit->equals(new Amount('0'))) {
            call_user_func($benefitCallback, $this, $benefit);
        }
        
        // Set and return verification
        $this->setVerification($ver);
        $this->setPaidDate($ver->getDate());
        $this->lock();

        return $this->getVerification();
    }


    /**
     * Export invoice Verification and mark invoice as exported
     *
     * @return Verification
     *
     * @throws EconomyException if invoice is not paid
     */
    public function exportToAccounting()
    {
        $ver = $this->getVerification();
        $this->setExported();

        return $ver;
    }


    /**
     * Check if invoice has been exported
     *
     * @return bool
     */
    public function isExported()
    {
        return isset($this->_dateExported);
    }


    /**
     * Set date when invoice was exported
     *
     * @param DateTime $date
     *
     * @return void
     */
    public function setExported(DateTime $date = NULL)
    {
        if (!$date) {
            $date = new DateTime();
        }
        $this->_dateExported = $date;
        $this->_dateExported->setTimezone($this->getTimezone());
    }


    /**
     * Get date when invoice was exported
     *
     * @return DateTime
     */
    public function getExportedDate()
    {
        if (!isset($this->_dateExported)) {
            return new NullDate();
        }

        return $this->_dateExported;
    }


    /**
     * Set date when invoice was paid
     *
     * Should not be called directly. Use pay()
     *
     * @param DateTime $date
     *
     * @return void
     */
    private function setPaidDate(DateTime $date)
    {
        $this->_datePaid = $date;
        $this->_datePaid->setTimezone($this->getTimezone());
    }

}