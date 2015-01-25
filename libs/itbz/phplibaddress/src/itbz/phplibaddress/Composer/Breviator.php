<?php
/**
 * This file is part of the phplibaddress package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package phplibaddress\Composer
 */

namespace itbz\phplibaddress\Composer;

/**
 * Abbreviate addressee information to fit within 26 characters
 * 
 * @package phplibaddress\Composer
 */
class Breviator
{
    /**
     * Concatenate given names, surname and titel
     * 
     * Ensures that the target string is no longer than 36 characters.
     * Transforms names to initials or removes them if neccesary.
     * 
     * @param string $names
     * @param string $surname
     * @param string $title
     * 
     * @return string
     */
    public function concatenate($names = '', $surname = '', $title = '')
    {
        assert('is_string($names)');
        assert('is_string($surname)');
        assert('is_string($title)');

        $fullname = trim("$title $names $surname");

        // Recursively remove characters so that strlen($fullname) < 36
        if (mb_strlen($fullname) > 36) {

            // Shorten $names if > 0
            if (mb_strlen($names) > 0) {
                $names = $this->abbrNames($names);

            } elseif (mb_strlen($title) > 0) {
                // Else remove title if it exists
                $title = '';

            } else {
                // Else remove one surname
                $newSurname = preg_replace("/^[^ ]* (.*)$/", "$1", $surname);
                if (mb_strlen($newSurname) >= mb_strlen($surname)) {

                    // Last fallback, remove one trailing char from surname
                    $newSurname = mb_substr($newSurname, 0, -1);
                }
                $surname = $newSurname;
            }

            return $this->concatenate($names, $surname, $title);
        }

        // Break recursion
        return $fullname;
    }

    /**
     * Shorten a string of names
     * 
     * @param string $names
     * 
     * @return string
     */
    private function abbrNames($names)
    {
        $arNames = explode(' ', $names);

        // If there is only one name, push it to second position
        if (count($arNames) == 1) {
            $arNames[1] = $arNames[0];
            unset($arNames[0]);
        }

        // Shorten or remove names, leave first name
        foreach ($arNames as $key => &$name) {
            if ($key == 0) {
                continue;
            }

            // Unset initials if they exist
            if (mb_strlen($name) == 1) {
                unset($arNames[$key]);

            } else {
                // Else create initials from complete names
                $name = trim($name);
                $name = mb_substr($name, 0, 1);
                $name = mb_strtoupper($name);
            }
        }

        $newNames = implode(' ', $arNames);

        // Assert that the returned string really is shorter
        assert('mb_strlen($newNames) < mb_strlen($names)');

        return $newNames;
    }
}
