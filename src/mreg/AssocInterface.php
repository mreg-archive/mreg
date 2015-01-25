<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg
 */

namespace mreg;


/**
 * Identifiers for associated content
 *
 * @package mreg
 */
interface AssocInterface
{

    /**
     * Address association id
     */
    const ASSOC_ADDRESS = 'address';


    /**
     * Mail association id
     */
    const ASSOC_MAIL = 'mail';


    /**
     * Phone association id
     */
    const ASSOC_PHONE = 'phone';


    /**
     * Revision association id
     */
    const ASSOC_REVISION = 'revision';

}