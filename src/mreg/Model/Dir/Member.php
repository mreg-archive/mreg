<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Dir
 */

namespace mreg\Model\Dir;

use DateTime;
use itbz\stb\ID\PersonalId;
use mreg\NullObject\EmptyPersonalId;
use itbz\stb\ID\CoordinationId;
use itbz\stb\ID\FakeId;
use itbz\stb\ID\PersonalIdBuilder;
use itbz\stb\Utils\Amount;
use itbz\stb\Banking\AccountBuilder;
use itbz\stb\Banking\AbstractAccount;
use mreg\NullObject\EmptyAccount;
use mreg\NullObject\NullDate;
use itbz\httpio\Request;

/**
 * Models an individual member
 *
 * @package mreg\Model\Dir
 */
class Member extends DirModel
{

    /**
     * Swedish personal id number of member
     *
     * @var PersonalId
     */
    private $_personalId;


    /**
     * Date of birth
     *
     * @var DateTime
     */
    private $_dob;


    /**
     * Sex of member. 'F' for female, 'M' for male, 'O' for other.
     *
     * @var string
     */
    private $_sex;


    /**
     * Member names
     *
     * @var string
     */
    private $_names;


    /**
     * Member surname
     *
     * @var string
     */
    private $_surname;


    /**
     * 'ANSTALLD','PENSIONAR','STUDERANDE','EGENFORETAGARE','ARBETSLOS','ANNAN'
     *
     * @var string
     */
    private $_workCondition = 'ANSTALLD';


    /**
     * Member salary
     *
     * @var Amount
     */
    private $_salary;


    /**
     * String describing debit class at last invoice build
     *
     * @var string
     */
    private $_debitClass = '';


    /**
     * String describing payment type
     *
     * enum('AG', 'BAG', 'BAG-V', 'MAG', 'MAG-V', 'PAG', 'LS')
     *
     * @var string
     */
    private $_paymentType = 'LS';


    /**
     * Member bank account
     *
     * @var AbstractAccount
     */
    private $_bankAccount;


    /**
     * Cached name of LS
     *
     * @var string
     */
    private $_ls = '';


    /**
     * Flag if member has expired and unpaid invoices
     *
     * @var bool
     */
    private $_invoiceFlag = FALSE;


    /**
     * Clone internal objects
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        if (isset($this->_personalId)) {
            $this->_personalId = clone $this->_personalId;
        }
        if (isset($this->_dob)) {
            $this->_dob = clone $this->_dob;
        }
        if (isset($this->_salary)) {
            $this->_salary = clone $this->_salary;
        }
        if (isset($this->_bankAccount)) {
            $this->_bankAccount = clone $this->_bankAccount;
        }
    }


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
        
        if (isset($data['personalId'])) {
            $this->setPersonalId($data['personalId']);
        }
        if (isset($data['dob'])) {
            $this->setDateOfBirth($data['dob']);
        }
        if (isset($data['sex'])) {
            $this->setSex($data['sex']);
        }
        if (isset($data['names'])) {
            $this->setNames($data['names']);
        }
        if (isset($data['surname'])) {
            $this->setSurname($data['surname']);
        }
        if (isset($data['workCondition'])) {
            $this->setWorkCondition($data['workCondition']);
        }
        if (isset($data['salary'])) {
            $this->setSalary($data['salary']);
        }
        if (isset($data['debitClass'])) {
            $this->setDebitClass($data['debitClass']);
        }
        if (isset($data['paymentType'])) {
            $this->setPaymentType($data['paymentType']);
        }
        if (isset($data['bankAccount'])) {
            $this->setBankAccount($data['bankAccount']);
        }
        if (isset($data['LS'])) {
            $this->setCachedLsName($data['LS']);
        }
        if (isset($data['invoiceFlag'])) {
            $flag = $data['invoiceFlag'] == '1' ? TRUE : FALSE;
            $this->setInvoiceFlag($flag);
        }
    }


    /**
     * Set Swedish personal id number of member
     *
     * @param string $personalId
     *
     * @return void
     */
    public function setPersonalId($personalId)
    {
        $idBuilder = new PersonalIdBuilder();
        $this->_personalId = $idBuilder->enableCoordinationId()
            ->enableFakeId()
            ->setId($personalId)
            ->getId();
    }


    /**
     * Get Swedish personal id number of member
     *
     * @return PersonalId
     */
    public function getPersonalId()
    {
        if (!isset($this->_personalId)) {

            return new EmptyPersonalId();
        }

        return $this->_personalId;
    }


    /**
     * Set date of birth
     *
     * @param string $dob 'YYYY-MM-DD'
     *
     * @return void
     */
    public function setDateOfBirth($dob)
    {
        $this->_dob = DateTime::createFromFormat('Y-m-d', $dob);
        $this->_dob->setTimezone($this->getTimezone());
    }


    /**
     * Get date of birth
     *
     * @return DateTime
     */
    public function getDateOfBirth()
    {
        if (!isset($this->_dob)) {
            $this->setDateOfBirth($this->getPersonalId()->getDOB());
        }

        return $this->_dob;
    }


    /**
     * Set sex of member
     *
     * 'F' for female, 'M' for male, 'O' for other.
     *
     * @param string $sex
     *
     * @return void
     */
    public function setSex($sex)
    {
        assert('is_string($sex)');
        $this->_sex = $sex;
    }


    /**
     * Get sex of member. 'F' for female, 'M' for male, 'O' for other.
     *
     * @return string
     */
    public function getSex()
    {
        if (!isset($this->_sex)) {
            $this->setSex($this->getPersonalId()->getSex());
        }

        return $this->_sex;
    }


    /**
     * Set names
     *
     * @param string $names
     *
     * @return void
     */
    public function setNames($names)
    {
        assert('is_string($names)');
        $this->_names = $names;
    }


    /**
     * Get names
     *
     * @return string
     */
    public function getNames()
    {
        return $this->_names;
    }


    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return void
     */
    public function setSurname($surname)
    {
        assert('is_string($surname)');
        $this->_surname = $surname;
    }


    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->_surname;
    }


    /**
     * Set work condition
     *
     * @param string $workCondition
     *
     * @return void
     */
    public function setWorkCondition($workCondition)
    {
        assert('is_string($workCondition)');
        $this->_workCondition = $workCondition;
    }


    /**
     * Get member work condition
     *
     * 'ANSTALLD','PENSIONAR','STUDERANDE','EGENFORETAGARE','ARBETSLOS','ANNAN'
     *
     * @return string
     */
    public function getWorkCondition()
    {
        return $this->_workCondition;
    }


    /**
     * Set salary
     *
     * @param string $salary
     *
     * @return void
     */
    public function setSalary($salary)
    {
        $this->_salary = new Amount('0');
        $this->_salary->setLocaleString($salary);
    }


    /**
     * Get salary
     *
     * @return Amount
     */
    public function getSalary()
    {
        if (!isset($this->_salary)) {
            $this->_salary = new Amount(0);
        }

        return $this->_salary;
    }


    /**
     * Set debit class
     *
     * @param string $debitClass
     *
     * @return void
     */
    public function setDebitClass($debitClass)
    {
        assert('is_string($debitClass)');
        $this->_debitClass = $debitClass;
    }


    /**
     * Get string describing debit class at last invoice build
     *
     * @return string
     */
    public function getDebitClass()
    {
        return $this->_debitClass;
    }


    /**
     * Set payment type
     *
     * enum('AG', 'BAG', 'BAG-V', 'MAG', 'MAG-V', 'PAG', 'LS')
     *
     * @param string $paymentType
     *
     * @return void
     */
    public function setPaymentType($paymentType)
    {
        assert('is_string($paymentType)');
        $this->_paymentType = $paymentType;
    }


    /**
     * Get string describing payment type
     *
     * enum('AG', 'BAG', 'BAG-V', 'MAG', 'MAG-V', 'PAG', 'LS')
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->_paymentType;
    }


    /**
     * Set bank account number
     *
     * @param string $bankAccount
     *
     * @return void
     */
    public function setBankAccount($bankAccount)
    {
        $accountBuilder = new AccountBuilder();
        $this->_bankAccount = $accountBuilder->enable('FakeAccount')
            ->setAccount($bankAccount)
            ->getAccount();
    }


    /**
     * Get bank account number
     *
     * @return AbstractAccount
     */
    public function getBankAccount()
    {
        if (!isset($this->_bankAccount)) {

            return new EmptyAccount();
        }

        return $this->_bankAccount;
    }


    /**
     * Set cached ls name
     *
     * @param string $ls
     *
     * @return void
     */
    public function setCachedLsName($ls)
    {
        assert('is_string($ls)');
        $this->_ls = $ls;
    }


    /**
     * Get cached name of ls
     *
     * @return string
     */
    public function getCachedLsName()
    {
        return $this->_ls;
    }


    /**
     * Check if member is flaged for expired invoices
     *
     * @return bool
     */
    public function hasExpiredInvoices()
    {
        return $this->_invoiceFlag;
    }


    /**
     * Flag member for expired invoices
     *
     * @param bool $flag
     *
     * @return void
     */
    public function setInvoiceFlag($flag)
    {
        assert('is_bool($flag)');
        $this->_invoiceFlag = $flag;
    }


    /**
     * Get title for this entity
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getNames() . ' ' . $this->getSurname();
    }


    /**
     * Get type descriptor
     *
     * @return string
     */
    public function getType()
    {
        return 'type_member';
    }


    /**
     * Get array describing addresse
     *
     * @return array
     */
    public function getAddressee()
    {
        return array(
            'given_name' => $this->getNames(),
            'surname' => $this->getSurname()
        );
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        return parent::export() + array(
            'names' => $this->getNames(),
            'surname' => $this->getSurname(),
            'personalId' => $this->getPersonalId()->getId(),
            'dob' => $this->getDateOfBirth()->format(DATETIME::ATOM),
            'sex' => $this->getSex(),
            'workCondition' => $this->getWorkCondition(),
            'salary' => $this->getSalary()->format(),
            'debitClass' => $this->getDebitClass(),
            'paymentType' => $this->getPaymentType(),
            'bankAccount' => (string)$this->getBankAccount(),
            'bank' => $this->getBankAccount()->getType(),
            'notes' => $this->getNotes(),
            'ls' => $this->getCachedLsName(),
            'avatar' => $this->getAvatar(),
            'invoiceFlag' => $this->hasExpiredInvoices()
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
        return parent::extract($context, $using) + array(
            'names' => $this->getNames(),
            'surname' => $this->getSurname(),
            'personalId' => $this->getPersonalId(),
            'dob' => $this->getDateOfBirth()->format('Y-m-d'),
            'sex' => $this->getSex(),
            'workCondition' => $this->getWorkCondition(),
            'salary' => $this->getSalary(),
            'debitClass' => $this->getDebitClass(),
            'paymentType' => $this->getPaymentType(),
            'bankAccount' => $this->getBankAccount(),
            'ls' => $this->getCachedLsName(),
            'invoiceFlag' => $this->hasExpiredInvoices() ? '1' : '0'
        );
    }


    /**
     * Load data from request
     *
     * @param Request $req
     *
     * @return void
     */
    public function loadRequest(Request $req)
    {
        $data = array();

        $data['personalId']
            = $req->body->get('personalId', FILTER_SANITIZE_STRING);
        
        $data['names'] = $req->body->get('names', FILTER_SANITIZE_STRING);

        $data['surname'] = $req->body->get('surname', FILTER_SANITIZE_STRING);

        if ( $req->body->is('workCondition') ) {
            $data['workCondition'] = $req->body->get(
                'workCondition',
                FILTER_SANITIZE_STRING
            );
        }
        if ( $req->body->is('sex') ) {
            $data['sex'] = $req->body->get('sex', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('notes') ) {
            $data['notes'] = $req->body->get('notes', FILTER_SANITIZE_STRING);
        }
        if ( $req->body->is('salary') ) {
            $data['salary']  = $req->body->get(
                'salary',
                FILTER_SANITIZE_STRING
            );
        }
        if ( $req->body->is('debitClass') ) {
             $data['debitClass']  = $req->body->get(
                 'debitClass',
                 FILTER_SANITIZE_STRING
             );
        }
        if ( $req->body->is('paymentType') ) {
             $data['paymentType']  = $req->body->get(
                 'paymentType',
                 FILTER_SANITIZE_STRING
             );
        }
        if ( $req->body->is('bankAccount') ) {
            $data['bankAccount']  = $req->body->get(
                'bankAccount',
                FILTER_SANITIZE_STRING
            );
        }
        if ( $req->body->is('avatar') ) {
            $data['avatar']  = $req->body->get(
                'avatar',
                FILTER_SANITIZE_STRING
            );
        }
        
        $this->load($data);
    }

}