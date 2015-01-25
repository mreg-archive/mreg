<?php
/**
 * This file is part of Mreg
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package mreg\Controller
 */

namespace mreg\Controller;

use mreg\Economy\Ledger;
use mreg\Mapper\AccountantMapper;
use mreg\Dispatch;
use mreg\View\JsonView;
use DateTime;
use itbz\httpio\Response;
use itbz\datamapper\pdo\access\AcMapper;
use itbz\datamapper\pdo\Search;
use itbz\datamapper\pdo\Expression;
use itbz\utils\CsvWriter;
use mreg\Exception\EconomyException;

/**
 * Controller for accountant import and export
 *
 * @package mreg\Controller
 */
class AccountantController extends FileController
{

    /**
     * Invoice ledger
     *
     * @var Ledger
     */
    private $_ledger;


    /**
     * Accountant mapper
     *
     * @var AccountantMapper
     */
    private $_accountantMapper;


    /**
     * Controller for accountant import and export
     *
     * @param Ledger $ledger
     *
     * @param AccountantMapper $accountantMapper
     *
     * @param string $uploadDir
     *
     * @param string $downloadDir
     */
    public function __construct(
        Ledger $ledger,
        AccountantMapper $accountantMapper,
        $uploadDir,
        $downloadDir
    )
    {
        parent::__construct($uploadDir, $downloadDir);
        $this->_ledger = $ledger;
        $this->_accountantMapper = $accountantMapper;
    }


    /**
     * Read accountant from ledger
     *
     * @return Response
     */
    public function read()
    {
        $accountant = $this->_ledger->getAccountant();

        // Export debits
        $debitClasses = array();
        foreach ($accountant->getDebits()->getClasses() as $debitClass) {
            $interval = $debitClass->getInterval();
            $debits = array();
            foreach ($debitClass->getDebits() as $name => $amount) {
                $debits[] = $name . ': ' . $amount->format();
            }
            $debitClasses[] = array(
                'name' => $debitClass->getName(),
                'charge' => $debitClass->getCharge()->format(),
                'interval' => array(
                    'start' => $interval[0]->format(),
                    'end' => $interval[1]->format()
                ),
                'debits' => implode(', ', $debits),
                'template' => $debitClass->getTemplateName()
            );
        }
       
        // Export templates
        $templates = array();
        foreach ($accountant->getTemplates()->getTemplates() as $template) {
            $templates[] = array(
                'id' => $template->getId(),
                'name' => $template->getName()
            );
        }

        // Export channels
        $channels = array();
        foreach ($accountant->getChannels()->getChannels() as $id => $account) {
            $channels[$id] = array(
                'nr' => $account->getNumber(),
                'type' => $account->getType(),
                'name' => $account->getName()
            );
        }

        // Export accounts
        $accounts = array();
        foreach ($accountant->getAccounts()->getAccounts() as $account) {
            $accounts[] = array(
                'nr' => $account->getNumber(),
                'type' => $account->getType(),
                'name' => $account->getName()
            );
        }

        // Count active invoices
        $unpaid = $unprinted = $expired = $autogiro = 0;
        $iter = $this->_ledger->findInvoices(array('paidVia' => ''));
        foreach ($iter as $invoice) {
            $unpaid++;
            if (!$invoice->isPrinted()) {
                $unprinted++;
            }
            if ($invoice->isExpired()) {
                $expired++;
            }
            if ($invoice->isAutogiro()) {
                $autogiro++;
            }
        }
        
        $faction = $this->_ledger->getFaction();
        $view = new JsonView();

        return $view
            ->append(
                'counters',
                array(
                    'unpaid' => $unpaid,
                    'unprinted' => $unprinted,
                    'expired' => $expired,
                    'autogiro' => $autogiro
                )
            )
            ->append('title', 'Bokhållare ' . $faction->getName())
            ->append('avatar', $faction->getAvatar())
            ->append('debits', $debitClasses)
            ->append('templates', $templates)
            ->append('channels', $channels)
            ->append('accounts', $accounts)
            ->setReadable(TRUE)
            ->getResponse();
    }


    /**
     * Controller for billing members
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function billMembers(Dispatch $dispatch)
    {
        $request = $dispatch->getRequest();

        $spec = $request->body->get('spec', FILTER_SANITIZE_STRING);
        $date = new DateTime(
            $request->body->get('date', '/\d{4}-\d\d-\d\d/')
        );

        $count = $this->_ledger->billAllMembers($spec, $date);

        return new Response(
            json_encode($count . " fakturor skapades"),
            200,
            array('Content-Type', 'application/json')
        );
    }


    /**
     * Controller for printing invoices to csv
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     *
     * @throws EconomyException if no printable invoices exist
     */
    public function printInvoicesCsv(Dispatch $dispatch)
    {
        // Get unpaid and unprinted invoices
        $iter = $this->_ledger->findInvoices(
            array(
                'paidVia' => '',
                'tPrinted' => '0',
                'isAutogiro' => '0'
            )
        );

        // Create csv
        $csv = new CsvWriter();
        $csv->addRow(
            array(
                'TITEL',
                'FAKTURANUMMER',
                'OCR',
                'SUMMA',
                'FAKTURADATUM',
                'FÖRFALLODATUM',
                'FAKTURATEXT'
            )
        );

        // Process
        $count = 0;
        foreach ($iter as $invoice) {
            $csv->addRow(
                array(
                    $invoice->getTitle(),
                    $invoice->getId(),
                    (string)$invoice->getOCR(),
                    (string)$invoice->getAmount(),
                    $invoice->getCreated()->format('Y-m-d'),
                    $invoice->getExpireDate()->format('Y-m-d'),
                    $invoice->getDescription()
                )
            );
            $invoice->setPrinted();
            $this->_ledger->saveInvoice($invoice);
            $count++;
        }

        if ($count == 0) {
            $msg = "Hittade inga fakturor att skriva ut";
            throw new EconomyException($msg);
        }

        // Send file to download
        $header = $this->prepareDownload(
            $dispatch->getMap(),
            $csv->getCsv(),
            'fakturor.csv',
            'csv',
            'text/csv'
        );


        return new Response(
            json_encode($count . " fakturor skrevs ut"),
            200,
            array(
                'Link' => $header,
                'Content-Type' => 'application/json'
            )
        );
    }


    /**
     * Export paid invoices
     *
     * Returns a 204 response with a link pointing to the download
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function exportInvoices(Dispatch $dispatch)
    {
        $request = $dispatch->getRequest();
        $fresh = $request->body->is('fresh');
        $from = new DateTime($request->body->get('from', '/^\d{4}-\d\d-\d\d/'));
        $to = new DateTime($request->body->get('to', '/^\d{4}-\d\d-\d\d/'));

        // Send file to download
        $header = $this->prepareDownload(
            $dispatch->getMap(),
            $this->_ledger->export($from, $to, $fresh),
            'export.si',
            'si',
            'application/x-sie'
        );

        return new Response(
            json_encode("Export klar"),
            200,
            array(
                'Link' => $header,
                'Content-Type' => 'application/json'
            )
        );
    }


    /**
     * Export templates to visma kml format
     *
     * Returns a 204 response with a link pointing to the download
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function exportTemplates(Dispatch $dispatch)
    {
        $header = $this->prepareDownload(
            $dispatch->getMap(),
            $this->_ledger->getAccountant()->exportTemplates(),
            'konteringsmallar.kml',
            'kml',
            'application/x-kml'
        );

        return new Response('', 204, array('Link' => $header));
    }


    /**
     * Eexport accounts to visma sie format
     *
     * Returns a 204 response with a link pointing to the download
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function exportAccounts(Dispatch $dispatch)
    {
        $name = 'Kontoplan ' . $this->_ledger->getFaction()->getName();

        $header = $this->prepareDownload(
            $dispatch->getMap(),
            $this->_ledger->getAccountant()->exportAccounts($name),
            str_replace(' ', '-', $name) . '.kp',
            'kp',
            'application/x-sie'
        );

        return new Response('', 204, array('Link' => $header));
    }


    /**
     * Import templates from uploaded file
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function importTemplates(Dispatch $dispatch)
    {
        $upload = $dispatch->getRequest()->getNextUpload();
        $kml = $upload->getContents();

        $accountant = $this->_ledger->getAccountant();
        $accountant->importTemplates($kml);
        $this->_accountantMapper->save($accountant);

        $response = new Response();

        if (!$accountant->isValid($msg)) {
            $response->addWarning($msg);
        }

        $response->setContent('Import klar');
        
        return $response;
    }


    /**
     * Import accounts from uploaded file
     *
     * @param Dispatch $dispatch
     *
     * @return Response
     */
    public function importAccounts(Dispatch $dispatch)
    {
        $upload = $dispatch->getRequest()->getNextUpload();
        $sie = $upload->getContents();

        $accountant = $this->_ledger->getAccountant();
        $accountant->importAccounts($sie);
        $this->_accountantMapper->save($accountant);

        $response = new Response();

        if (!$accountant->isValid($msg)) {
            $response->addWarning($msg);
        }

        $response->setContent('Import klar');
        
        return $response;
    }

}
