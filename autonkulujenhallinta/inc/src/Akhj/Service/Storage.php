<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Service;

/**
 * Class Storage
 *
 * @package Akhj\Service
 */
class Storage {
    
    /**
     * Avain, jolla entitymanager tallennetaan muistiin
     */
    const KEY_ENTITY_MANAGER = "entityManager";
    
    /**
     * Avain, jolla asetukset tallennetaan muistiin
     */
    const KEY_APP_CONFIG = "appConfig";

    /**
     * Tallennettavien tietojen säilytyspaikka
     *
     * @var array
     */
    private static $storage = array();

    /**
     * Privaatti konstruktori, ettei tätä luokkaa voi käyttää oliona
     */
    private function __construct() {}

    /**
     * Palauttaa tallennetun tiedot, tai null jos ei löydy
     *
     * @param string $key Avain jolla tieto haetaan
     * @return mixed|null
     */
    public static function get($key) {
        if (isset(self::$storage[$key])) {
            return self::$storage[$key];
        } else {
            return null;
        }
    }

    /**
     * Tallentaa annetun tiedon
     *
     * @param string $key Avain jolla tieto tallennetaan
     * @param mixed $value Tallennettava tieto
     */
    public static function set($key, $value) {
        if (self::$storage === null || !is_array(self::$storage)) {
            self::$storage = array();
        }
        self::$storage[$key] = $value;
    }

}