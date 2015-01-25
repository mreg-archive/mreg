<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use itbz\httpio\Response;
use itbz\datamapper\pdo\access\AcMapper;
use mreg\Exception;
use mreg\Exception\HTTP\PreconditionFailedException;
use mreg\View\JsonView;
use mreg\View\JsonListView;
use mreg\Dispatch;
use mreg\Mapper\RevisionMapper;
use mreg\Economy\Ledger;
use mreg\Exception\EconomyException;
use itbz\stb\Utils\Amount;
use DateTime;

/**
 * Invoice controller
 *
 * @package mreg\Controller
 */
class InvoiceController implements \mreg\AssocInterface
{

    /**
     * Invoice mapper
     *
     * @var AcMapper
     */
    private $_invoiceMapper;


    /**
     * Revisions mapper
     *
     * @var RevisionMapper
     */
    private $_revisionMapper;


    /**
     * Invoice ledger
     *
     * @var Ledger
     */
    private $_ledger;


    /**
     * Invoice controller
     *
     * @param AcMapper $mapper
     *
     * @param RevisionMapper $revMapper
     *
     * @param Ledger $ledger
     */
    public function __construct(
        AcMapper $mapper,
        RevisionMapper $revMapper,
        Ledger $ledger
    )
    {
        $this->_invoiceMapper = $mapper;
        $this->_revisionMapper = $revMapper;
        $this->_ledger = $ledger;
    }


    /**
     * Controller for reading an invoice
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function read(Dispatch $dispatch)
    {
        $user = $dispatch->getUser();
        $map = $dispatch->getMap();
        $route = $dispatch->getRoute();
        $this->_invoiceMapper->setUser($user->getName(), $user->getGroups());
        $invoice = $this->_invoiceMapper->findByPk($route->values['id']);
        $view = new JsonView($invoice);
        
        return $view
            ->setLink('self', $map->generate('member-invoices', $route->values))
            ->setLink('edit', $map->generate('member-invoices', $route->values))
            ->append(
                'recipient',
                array(
                    'id' => $invoice->getRecipientId(),
                    'link' => $map->generate(
                        'factions.main',
                        array('mainId' => $invoice->getRecipientId())
                    )
                )
            )
            ->append(
                'payer',
                array(
                    'id' => $invoice->getPayerId(),
                    'link' => $map->generate(
                        'members.main',
                        array('mainId' => $invoice->getPayerId())
                    )
                )
            )
            ->append(
                'modifiedBy',
                array(
                    'name' => $invoice->getModifiedBy(),
                    'link' => $map->generate(
                        'user',
                        array('name' => $invoice->getModifiedBy())
                    )
                )
            )
            ->append(
                'owner',
                array(
                    'name' => $invoice->getOwner(),
                    'link' => $map->generate(
                        'user',
                        array('name' => $invoice->getOwner())
                    )
                )
            )
            ->append(
                'group',
                array(
                    'name' => $invoice->getGroup(),
                    'link' => $map->generate(
                        'group',
                        array('name' => $invoice->getGroup())
                    )
                )
            )
            ->append('mode', $invoice->getMode())
            ->append(
                'revisions',
                $this->_revisionMapper->findRevisions(
                    $route->values['id']
                )
            )
            ->setReadable(TRUE)
            ->getResponse();
    }


    /**
     * Controller for updating a main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws EconomyException if invoice is locked
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function update(Dispatch $dispatch)
    {
        $invoice = $this->_invoiceMapper->findByPk(
            $dispatch->getRoute()->values['id']
        );

        if ($invoice->isLocked()) {
            $msg = "Kan inte uppdatera låst faktura";
            throw new EconomyException($msg);
        }

        $request = $dispatch->getRequest();

        if (!$request->matchEtag($invoice->getEtag())) {
            $msg = "Kan ej uppdatera: du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }
 
        // Pay invoice
        if ($request->body->is('paidVia') && !$invoice->isPaid()) {
            $channel = $request->body->get('paidVia', FILTER_SANITIZE_STRING);
            if ($channel != '') {
                $amount = new Amount();
                $amount->setLocaleString(
                    $request->body->get('paidAmount', '/^(\d|\s)+,?\d{0,2}$/')
                );
                $claim = $request->body->is('billClaim');
                $tPaid = new DateTime(
                    $request->body->get('tPaid', '/^\d{4}-\d\d-\d\d$/')
                );
                if ($channel == 'K') {
                    $this->_ledger->payK($invoice, $amount, $claim, $tPaid);
                } elseif ($channel == 'PG') {
                    $this->_ledger->payPg($invoice, $amount, $claim, $tPaid);
                } elseif ($channel == 'BG') {
                    $this->_ledger->payBg($invoice, $amount, $claim, $tPaid);
                } elseif ($channel == 'AG') {
                    $this->_ledger->payAg($invoice, $amount, $claim, $tPaid);
                }

                return new Response(
                    json_encode("Faktura #{$invoice->getId()} betalades"),
                    200,
                    array('Content-Type' => 'application/json')
                );
            }
        }

        // Mark as printed
        if ($request->body->is('markAsPrinted') && !$invoice->isPrinted()) {
            $invoice->setPrinted();
        }

        // Mark as exported
        if ($request->body->is('markAsExported') && !$invoice->isExported()) {
            $invoice->setExported();
        }

        // Set desctiption
        if ($request->body->is('description')) {
            $invoice->setDescription(
                $request->body->get(
                    'description',
                    FILTER_SANITIZE_STRING
                )
            );
        }

        // Set expire
        if ($request->body->is('tExpire')) {
            $invoice->setExpire(
                new DateTime(
                    $request->body->get(
                        'tExpire',
                        '/^\d{4}-\d\d-\d\d$/'
                    )
                )
            );
        }

        $invoice->setModifiedBy($dispatch->getUser()->getName());
        $invoice = $this->_invoiceMapper->save($invoice);
        
        return new Response('', 204);
    }


    /**
     * Controller for deleting a main entity
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws EconomyException if invoice is locked
     *
     * @throws PreconditionFailedException if etag is not valid
     */
    public function delete(Dispatch $dispatch)
    {
        $invoice = $this->_invoiceMapper->findByPk(
            $dispatch->getRoute()->values['id']
        );

        if ($invoice->isLocked()) {
            $msg = "Kan inte radera låst faktura";
            throw new EconomyException($msg);
        }

        if (!$dispatch->getRequest()->matchEtag($invoice->getEtag())) {
            $msg = "Kan ej radera: du arbetar inte med senaste versionen";
            throw new PreconditionFailedException($msg);
        }

        $this->_invoiceMapper->delete($invoice);
        
        return new Response('', 204);
    }

}
