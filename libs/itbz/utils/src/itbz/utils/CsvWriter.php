<?php
/**
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package utils
 */

namespace itbz\utils;

/**
 * Class to create csv files.
 *
 * NOTE: CsvWriter uses mb_strlen() to align columns. You might need
 * to set your encoding using mb_internal_encoding() for this to work
 * properly with non-english characters.
 *
 * @package utils
 */
class CsvWriter
{
    /**
     * Field delimiter
     *
     * @var string
     */
    private $delim;

    /**
     * Field enclosure character
     *
     * @var string
     */
    private $enclosure;

    /**
     * New line character
     *
     * @var string
     */
    private $newline;

    /**
     * Flag if output should be aligned
     *
     * @var bool
     */
    private $align;

    /**
     * Save max size of each column in dataset
     *
     * @var array
     */
    private $colSizes = array();

    /**
     * Data set
     *
     * @var array
     */
    private $data = array();

    /**
     * Set delim, enclosure and newline on construct
     *
     * @param string $delim Field delimiter
     * @param string $enclosure Field enclosure character
     * @param string $newline New line character
     * @param bool $align True if output should be aligned
     */
    public function __construct(
        $delim = ',',
        $enclosure = '"',
        $newline = "\r\n",
        $align = true
    ) {
        assert('is_string($delim)');
        assert('is_string($enclosure)');
        assert('is_string($newline)');
        assert('is_bool($align)');
        $this->delim = $delim;
        $this->enclosure = $enclosure;
        $this->newline = $newline;
        $this->align = $align;
    }

    /**
     * Add a new row of data
     *
     * @param array $row
     *
     * @return void
     */
    public function addRow(array $row)
    {
        $index = 0;
        foreach ($row as &$field) {

            // Each field should be escaped and enclosed
            $e = $this->enclosure;
            $field = preg_replace("/$e(.+)$e/", "$e$e$1$e$e", $field);
            $field = sprintf("$e%s$e", $field);

            // Save number of columns
            if (!isset($this->colSizes[$index])) {
                $this->colSizes[$index] = 0;
            }

            // Save maximum column size
            if ($this->align) {
                $size = mb_strlen($field);
                if ($size > $this->colSizes[$index]) {
                    $this->colSizes[$index] = $size;
                }
            }

            $index++;
        }

        // Save data
        $this->data[] = $row;
    }

    /**
     * Bulk add multiple rows of data
     *
     * @param array $data Array of arrays of data
     *
     * @return void
     */
    public function addData(array $data)
    {
        foreach ($data as $row) {
            $this->addRow($row);
        }
    }

    /**
     * Get data as csv
     *
     * @return string
     */
    public function getCsv()
    {
        // The return string
        $str = "";

        // Loop through data
        foreach ($this->data as $row) {
            // Each row should have the same number of columns
            while (count($row) < count($this->colSizes)) {
                $row[] = '';
            }

            // Align fields
            if ($this->align) {
                foreach ($row as $index => &$field) {
                    $missing = $this->colSizes[$index] - mb_strlen($field);
                    for ($i=0; $i<$missing; $i++) {
                        $field .= ' ';
                    }
                }
            }

            // Implode data
            $strRow = implode($this->delim, $row);
            $str .= $strRow . $this->newline;
        }

        return $str;
    }
}
