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
 * Create indented json
 *
 * @package utils
 */
class JsonFormatter
{
    /**
     * Convert a php variable to indented json
     *
     * @param mixed $input
     * @param string $indentStr Indention string useds
     * @param string $newLine New line character used
     *
     * @return string
     */
    public static function format($input, $indentStr = '  ', $newLine = "\n")
    {
        $json = json_encode($input);

        return self::formatJson($json, $indentStr, $newLine);
    }

    /**
     * Indent a json string
     *
     * @param string $json Must be a valid json string
     * @param string $indentStr Indention string useds
     * @param string $newLine New line character used
     *
     * @return string
     */
    public static function formatJson($json, $indentStr = '  ', $newLine = "\n")
    {
        return self::indent($json, $indentStr, $newLine);
    }

    /**
     * Private indenter.
     *
     * From http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     *
     * @param string $json
     * @param string $indentStr
     * @param string $newLine
     *
     * @return string
     */
    private static function indent($json, $indentStr = '  ', $newLine = "\n")
    {
        $result = '';
        $pos = 0;
        $strLen = mb_strlen($json);
        $prevChar = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {
            // Grab the next character in the string.
            $char = mb_substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

            } elseif (($char == '}' || $char == ']') && $outOfQuotes) {
                // If this character is the end of an element,
                // output a new line and indent the next line.
                $result .= $newLine;
                $pos--;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (
                ($char == ',' || $char == '{' || $char == '[') && $outOfQuotes
            ) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}
