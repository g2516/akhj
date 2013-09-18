<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Util;

/**
 * StringHelper -luokassa on merkkijonojen käsittelyyn apufunktioita
 *
 * @package Akhj\Util
 * @author Mikko Rainio
 */
class StringHelper {

    /**
     * Tarkistaa onko merkkijono tyhjä tai null
     * @param String $str Tarkistettava
     * @return boolean 
     */
    public static final function isBlank($str) {
        if ($str === null || (is_string($str) && strlen($str) === 0) ) {
            return true;
        }
        return false;
    }

    /**
     * Tarkistaa onko merkkijono UPPERCASE
     * @param String $input Tarkistettava
     * @return boolean
     */
    public static final function is_upper($input) {
        return ($input === strtoupper($input));
    }

    /**
     * Tarkistaa onko merkkijono lowercase
     * @param String $input Tarkistettava
     * @return boolean
     */
    public static final function is_lower($input) {
        return ($input === strtolower($input));
    }

    /**
     * Muuttaa merkkijonon lowercase => UPPERCASE
     * @param String $str
     * @return String
     */
    public static final function strToUpper($str) {
        if (is_string($str)) {
            return mb_strtoupper($str, "UTF-8");
        } else {
            return $str;
        }
    }

    /**
     * Muuttaa merkkijonon UPPERCASE => lowercase
     * @param String $str
     * @return String
     */
    public static final function strToLower($str) {
        if (is_string($str)) {
            return mb_strtolower($str, "UTF-8");
        } else {
            return $str;
        }
    }

    /**
     * Muuttaa merkkijonon ensimmäisen kirjaimen isoksi kirjaimeksi
     * @param String $str
     * @return String
     */
    public static final function ucFirst($str) {
        if (!self::isBlank($str)) {
            return ucfirst($str);
        } else {
            return $str;
        }
    }

    /**
     * search backwards for needle in haystack, and return its position
     * 
     * @param String $haystack
     * @param String $needle
     * @param int $offset
     * @return int
     */
    public static final function rstrpos($haystack, $needle, $offset=0){
        $size = strlen($haystack);
        $pos = strpos(strrev($haystack), $needle, $size - $offset);

        if ($pos === false)
            return -1;

        return $size - $pos;
    }
}