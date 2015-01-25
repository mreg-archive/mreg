<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Economy
 */

namespace mreg\Economy;

use itbz\httpio\Request;


/**
 * Mreg member invoice
 *
 * @package mreg\Economy
 */
class MemberInvoice extends AbstractInvoice
{

    /**
     * Get type descriptor
     *
     * @return string
     */
    public function getType()
    {
        return 'type_invoice_member';
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
    }

}