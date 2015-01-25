<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Response;
use itbz\datamapper\pdo\Search;
use itbz\datamapper\pdo\Expression;
use mreg\Exception;
use mreg\Mapper\AccountantMapper;
use mreg\Mapper\DirMapper;
use mreg\Mapper\XrefMapper;
use mreg\Economy\TableOfDebits;
use mreg\Economy\Channels;
use mreg\Model\Dir\Faction;
use mreg\Dispatch;

/**
 * Controller for creating default accountants
 *
 * @package mreg\Controller
 */
class CreateAccountantController
{

    /**
     * Accountant mapper
     *
     * @var AccountantMapper
     */
    private $_accountantMapper;


    /**
     * Faction mapper
     *
     * @var DirMapper
     */
    private $_factionMapper;


    /**
     * Mapper for referenced factions
     *
     * @var XrefMapper
     */
    private $_factionXrefMapper;


    /**
     * VISMA kml formatted string of default templates
     *
     * @var string
     */
    private $_defaultTemplates;


    /**
     * SIE formatted string of default chart of accounts
     *
     * @var string
     */
    private $_defaultAccounts;


    /**
     * Default channels
     *
     * @var Channels
     */
    private $_defaultChannels;


    /**
     * Default table of debits
     *
     * @var TableOfDebits
     */
    private $_defaultDebits;


    /**
     * Controller for accountant import and export
     *
     * @param AccountantMapper $accountantMapper
     *
     * @param DirMapper $factionMapper
     *
     * @param XrefMapper $factionXrefMapper
     *
     * @param string $defaultTemplates VISMA kml formatted string
     *
     * @param string $defaultAccounts SIE formatted string
     *
     * @param Channels $defaultChannels
     *
     * @param TableOfDebits $defaultDebits
     */
    public function __construct(
        AccountantMapper $accountantMapper,
        DirMapper $factionMapper,
        XrefMapper $factionXrefMapper,
        $defaultTemplates,
        $defaultAccounts,
        Channels $defaultChannels,
        TableOfDebits $defaultDebits
    )
    {
        $this->_accountantMapper = $accountantMapper;
        $this->_factionMapper = $factionMapper;
        $this->_factionXrefMapper = $factionXrefMapper;
        $this->_defaultTemplates = $defaultTemplates;
        $this->_defaultAccounts = $defaultAccounts;
        $this->_defaultChannels = $defaultChannels;
        $this->_defaultDebits = $defaultDebits;
    }


    /**
     * Create new accountant for faction
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws Exception if user is not root
     *
     * @throws Exception if faction already has accountant
     *
     * @throws Exception if accountantParentId could not be calculated
     */
    public function create(Dispatch $dispatch)
    {
        // Only roots can create
        $user = $dispatch->getUser();
        if (!$user->isRoot()) {
            $msg = "Endast root kan skapa bokhållare";
            throw new Exception($msg);
        }
        
        $this->_factionMapper->setAuthUser($user);
        $faction = $this->_factionMapper->findByPk(
            $dispatch->getRequest()->body->get('factionId', FILTER_VALIDATE_INT)
        );

        if ($faction->getAccountantId()) {
            $msg = "{$faction->getName()} har redan en bokhållare";
            throw new Exception($msg);
        }

        // Accountant parent faction
        $accountantParentId = 1;
        $iter = $this->_factionXrefMapper->findXrefMasters(
            $faction,
            new Search(),
            new Expression('state', 'OK')
        );
        
        foreach ($iter as $relation) {
            list(, $masterFaction) = $relation;
            if ($masterFaction->getType() == 'type_faction_DISTRIKT') {
                $accountantParentId = $masterFaction->getAccountantId();
                if (!$accountantParentId) {
                    $msg = "Kan ej skapa bokhållare för {$faction->getName()}:";
                    $msg .= " {$masterFaction->getName()} saknar bokhållare";
                    throw new Exception($msg);
                }
                break;
            }
        }

        // Create accountant
        $accountant = $this->_accountantMapper->getNewModel();
        $accountant->importTemplates($this->_defaultTemplates);
        $accountant->importAccounts($this->_defaultAccounts);
        $accountant->setChannels($this->_defaultChannels);
        $accountant->setDebits($this->_defaultDebits);
        $accountant->setParentId($accountantParentId);

        // Create response
        $response = new Response(
            json_encode("Bokförare för {$faction->getName()} skapades"),
            200,
            array('Content-Type' => 'application/json')
        );

        if (!$accountant->isValid($msg)) {
            $response->addWarning($msg);
        }

        // Save
        $this->_accountantMapper->save($accountant);
        $accountantId = $this->_accountantMapper->getLastInsertId();
        $faction->setAccountantId($accountantId);
        $this->_factionMapper->save($faction);

        return $response;
    }

}
