<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Aux
 */

namespace mreg\Model\Aux;

use itbz\httpio\Request;
use itbz\phplibphone\Number;
use DateTime;


/**
 * Phone model
 *
 * @package mreg\Model\Aux
 */
class Phone extends AuxModel
{

    /**
     * Phone number parser
     *
     * @var Number
     */
    private $_parser;


    /**
     * Name of carrier for this number
     *
     * @var string
     */
    private $_carrier = '';


    /**
     * Set parser
     *
     * @param Number $parser
     */
    public function __construct(Number $parser)
    {
        $this->_parser = $parser;
    }


    /**
     * Clone parser
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->_parser = clone $this->_parser;
    }


    /**
     * Get entity type
     *
     * @return string
     */
    public function getType()
    {
        return "type_phone";
    }


    /**
     * Get number
     *
     * return $string
     */
    public function getNr()
    {
        return $this->_parser->format();
    }


    /**
     * Set number new number
     *
     * Use this method to set user input numbers. Non numerical characters
     * (apart from prefixes) will be silently ignored. For swedish numbers
     * carrier information will be automatically updated if number was changed.
     *
     * @param string $nr
     *
     * @return void
     */
    public function setNr($nr)
    {
        $old = $this->_parser->getE164();
        $this->_parser->setRaw($nr);

        // Set carrier if changes were made
        if ($old != $this->_parser->getE164()) {
            $this->setCarrier($this->_parser->getCarrier());
        }
    }


    /**
     * Get name of carrier
     *
     * @return string
     */
    public function getCarrier()
    {
        return $this->_carrier;
    }


    /**
     * Set name of carrier
     *
     * @param string $carrier
     *
     * @return void
     */
    public function setCarrier($carrier)
    {
        assert('is_string($carrier)');
        $this->_carrier = $carrier;
    }


    /**
     * Get country description
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->_parser->getCountry();
    }


    /**
     * Get area description
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_parser->getArea();
    }


    /**
     * Check if number is structurally valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_parser->isValid();
    }
 

    /**
     * Check if number is a mobile number
     *
     * @return bool
     */
    public function isMobile()
    {
        return $this->_parser->getArea() === 'MOBILE';
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
        if (isset($data['cc'])) {
            $this->_parser->setCountryCode($data['cc']);
        }
        if (isset($data['ndc'])) {
            $this->_parser->setNationalDestinationCode($data['ndc']);
        }
        if (isset($data['sn'])) {
            $this->_parser->setSubscriberNumber($data['sn']);
        }
        if (isset($data['carrier'])) {
            $this->setCarrier($data['carrier']);
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
        if ($req->body->is('nr')) {
            $this->setNr($req->body->get('nr', FILTER_SANITIZE_STRING));
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
            'cc' => $this->_parser->getCountryCode(),
            'ndc' => $this->_parser->getNationalDestinationCode(),
            'sn' => $this->_parser->getSubscriberNumber(),
            'carrier' => $this->getCarrier(),
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
            'nr' => $this->getNr(),
            'valid' => $this->isValid(),
            'mobile' => $this->isMobile(),
            'country' => $this->getCountry(),
            'area' => $this->getArea(),
            'carrier' => $this->getCarrier(),
        );
    }

}