<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Aux
 */

namespace mreg\Model\Aux;

use EmailAddressValidator;
use itbz\httpio\Request;
use DateTime;


/**
 * Mail model
 *
 * Validates addresses using EmailAddressValidator
 *
 * @package mreg\Model\Aux
 */
class Mail extends AuxModel
{

    /**
     * Address validator
     *
     * @var EmailAddressValidator
     */
    private $_validator;


    /**
     * The mail address
     *
     * @var string
     */
    private $_mail = '';


    /**
     * Inject address validator
     *
     * @param EmailAddressValidator $validator
     */
    public function __construct(EmailAddressValidator $validator)
    {
        $this->_validator = $validator;
    }


    /**
     * Get entity type
     *
     * @return string
     */
    public function getType()
    {
        return "type_mail";
    }


    /**
     * Get mail address
     *
     * @return string
     */
    public function getMail()
    {
        return $this->_mail;
    }


    /**
     * Set mail address
     *
     * @param string $mail
     *
     * @return void
     */
    public function setMail($mail)
    {
        assert('is_string($mail)');
        $this->_mail = $mail;
    }


    /**
     * Check if mail is valid 
     *
     * @return bool TRUE if email is valid, FALSE if not
     */
    public function isValid()
    {
        return $this->_validator->check_email_address($this->getMail());
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
        if (isset($data['mail'])) {
            $this->setMail($data['mail']);
        }
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
        $this->setName($req->body->get('name', FILTER_SANITIZE_STRING));
        $this->setModified(new DateTime);
        if ($req->body->is('mail')) {
            $this->setMail($req->body->get('mail', FILTER_SANITIZE_STRING));
        }
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
            'mail' => $this->getMail(),
            'valid' => $this->isValid()
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
            'mail' => $this->getMail(),
            'valid' => $this->isValid()
        );
    }

}