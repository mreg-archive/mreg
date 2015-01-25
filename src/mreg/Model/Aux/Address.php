<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Model\Aux
 */

namespace mreg\Model\Aux;

use itbz\phplibaddress\Composer\AbstractComposer;
use itbz\httpio\Request;
use DateTime;


/**
 * Address model
 *
 * @package mreg\Model\Aux
 */
class Address extends AuxModel
{

    /**
     * Address composer object
     *
     * @var AbstractComposer
     */
    private $_composer;


    /**
     * Inject parser object
     *
     * @param AbstractComposer $composer
     */
    public function __construct(AbstractComposer $composer)
    {
        $this->_composer = $composer;
    }


    /**
     * Clone composer
     *
     * @return void
     */
    public function __clone()
    {
        parent::__clone();
        $this->_composer = clone $this->_composer;
    }


    /**
     * Get entity type
     *
     * @return string
     */
    public function getType()
    {
        return "type_address";
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

        $address = $this->_composer->getAddress();

        if (isset($data['mailee_role_descriptor'])) {
            $address->setMaileeRoleDescriptor($data['mailee_role_descriptor']);
        }
        if (isset($data['mailee'])) {
            $address->setNameOfMailee($data['mailee']);
        }
        if (isset($data['thoroughfare'])) {
            $address->setThoroughfare($data['thoroughfare']);
        }
        if (isset($data['plot'])) {
            $address->setPlot($data['plot']);
        }
        if (isset($data['littera'])) {
            $address->setLittera($data['littera']);
        }
        if (isset($data['stairwell'])) {
            $address->setStairwell($data['stairwell']);
        }
        if (isset($data['floor'])) {
            $address->setFloor($data['floor']);
        }
        if (isset($data['door'])) {
            $address->setDoor($data['door']);
        }
        if (isset($data['supplementary_delivery_point_data'])) {
            $address->setSupplementaryData(
                $data['supplementary_delivery_point_data']
            );
        }
        if (isset($data['delivery_service'])) {
            $address->setDeliveryService($data['delivery_service']);
        }
        if (isset($data['alternate_delivery_service'])) {
            $address->setAlternateDeliveryService(
                $data['alternate_delivery_service']
            );
        }
        if (isset($data['postcode'])) {
            $address->setPostcode($data['postcode']);
        }
        if (isset($data['town'])) {
            $address->setTown($data['town']);
        }
        if (isset($data['country_code']) && !empty($data['country_code'])) {
            $address->setCountryCode($data['country_code']);
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

        $address = $this->_composer->getAddress();

        if ($req->body->is('mailee_role_descriptor')) {
            $address->setMaileeRoleDescriptor(
                $req->body->get(
                    'mailee_role_descriptor',
                    FILTER_SANITIZE_STRING
                )
            );
        }
        if ($req->body->is('mailee')) {
            $address->setNameOfMailee(
                $req->body->get('mailee', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('thoroughfare')) {
            $address->setThoroughfare(
                $req->body->get('thoroughfare', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('plot')) {
            $address->setPlot($req->body->get('plot', FILTER_SANITIZE_STRING));
        }
        if ($req->body->is('littera')) {
            $address->setLittera(
                $req->body->get('littera', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('stairwell')) {
            $address->setStairwell(
                $req->body->get('stairwell', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('floor')) {
            $address->setFloor(
                $req->body->get('floor', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('door')) {
            $address->setDoor($req->body->get('door', FILTER_SANITIZE_STRING));
        }
        if ($req->body->is('supplementary_delivery_point_data')) {
            $address->setSupplementaryData(
                $req->body->get(
                    'supplementary_delivery_point_data',
                    FILTER_SANITIZE_STRING
                )
            );
        }
        if ($req->body->is('delivery_service')) {
            $address->setDeliveryService(
                $req->body->get('delivery_service', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('alternate_delivery_service')) {
            $address->setAlternateDeliveryService(
                $req->body->get(
                    'alternate_delivery_service',
                    FILTER_SANITIZE_STRING
                )
            );
        }
        if ($req->body->is('postcode')) {
            $address->setPostcode(
                $req->body->get('postcode', FILTER_SANITIZE_STRING)
            );
        }
        if ($req->body->is('town')) {
            $address->setTown($req->body->get('town', FILTER_SANITIZE_STRING));
        }
        if ($req->body->is('country_code')) {
            $address->setCountryCode(
                $req->body->get('country_code', FILTER_SANITIZE_STRING)
            );
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
        $address = $this->_composer->getAddress();

        return parent::extract($context, $using) + array(
            'mailee_role_descriptor' => $address->getMaileeRoleDescriptor(),
            'mailee' => $address->getNameOfMailee(),
            'thoroughfare' => $address->getThoroughfare(),
            'plot' => $address->getPlot(),
            'littera' => $address->getLittera(),
            'stairwell' => $address->getStairwell(),
            'floor' => $address->getFloor(),
            'door' => $address->getDoor(),
            'supplementary_delivery_point_data' =>
                $address->getSupplementaryData(),
            'delivery_service' => $address->getDeliveryService(),
            'alternate_delivery_service' =>
                $address->getAlternateDeliveryService(),
            'postcode' => $address->getPostcode(),
            'town' => $address->getTown(),
            'country_code' => $address->getCountryCode()
        );
    }


    /**
     * Export to client application
     *
     * @return array
     */
    public function export()
    {
        $address = $this->_composer->getAddress();

        return parent::export() + array(
            'mailee_role_descriptor' => $address->getMaileeRoleDescriptor(),
            'mailee' => $address->getNameOfMailee(),
            'thoroughfare' => $address->getThoroughfare(),
            'plot' => $address->getPlot(),
            'littera' => $address->getLittera(),
            'stairwell' => $address->getStairwell(),
            'floor' => $address->getFloor(),
            'door' => $address->getDoor(),
            'supplementary_delivery_point_data' =>
                $address->getSupplementaryData(),
            'delivery_service' => $address->getDeliveryService(),
            'alternate_delivery_service' =>
                $address->getAlternateDeliveryService(),
            'postcode' => $address->getPostcode(),
            'town' => $address->getTown(),
            'country_code' => $address->getCountryCode()
        );
    }


    /**
     * Get formatted address
     *
     * @param array $addressee
     *
     * @return string
     */
    public function getAddress(array $addressee)
    {
        $addressee = array_merge(
            array(
                'form' => '',
                'given_name' => '',
                'surname' => '',
                'organisation_name' => '',
                'legal_status' => '',
                'organisational_unit' => ''
            ),
            $addressee
        );

        $address = $this->_composer->getAddress();

        $address->setForm($addressee['form']);
        $address->setGivenName($addressee['given_name']);
        $address->setSurname($addressee['surname']);
        $address->setOrganisationName($addressee['organisation_name']);
        $address->setLegalStatus($addressee['legal_status']);
        $address->setOrganisationalUnit($addressee['organisational_unit']);

        return $this->_composer->getValid();
    }


    /**
     * Get formatted delivery point
     *
     * @return string
     */
    public function getDeliveryPoint()
    {
        return $this->_composer->getDeliveryPoint();
    }


    /**
     * Check if this address equals $addr.
     *
     * The parsed addresses are compared, individual fields may still differ.
     *
     * @param Address $addr
     *
     * @return bool
     */
    public function equals(Address $addr)
    {
        $a = mb_strtoupper($this->getDeliveryPoint());
        $b = mb_strtoupper($addr->getDeliveryPoint());

        return (strcmp($a, $b) === 0);
    }

}