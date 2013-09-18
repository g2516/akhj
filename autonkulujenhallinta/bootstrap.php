<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 * Tiedosto sisältää autoloader-funktion, järjestelmän vakioita sekä muuta
 * konfiguraatiota.
 *
 * @author Mikko Rainio
 */

require 'vendor/autoload.php';

// Annetaan sessio-keksille nimi
session_name("AKHJ_SESSIOID");
// Aloitetaan sessio
session_start();

/**
 * Järjestelmän juurihakemisto
 */
define("APPLICATION_DIR", dirname(__FILE__));
/**
 * Asetustiedoston polku
 */
define("CONFIG_DIR", APPLICATION_DIR . '/inc/config');
/**
 * Sivumallien polku
 */
define("TEMPLATES_DIR", APPLICATION_DIR . '/inc/templates');
/**
 * Onko ajossa kehitys- vai tuotantoympäristö
 */
define("DEVELOPMENT_MODE", isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development');

// Säädetään timezone oikeaksi
date_default_timezone_set('Europe/Helsinki');

// Asetetaan poikkeusten käsittelijä. Tämä käsittelee kaikki poikkeukset, joita
// ei oteta koodissa kiinni
$exceptionHandler = new Akhj\Exception\ExceptionHandler();
set_exception_handler(array($exceptionHandler, "handleException"));

/**
 * Käsitellään itse virheet jotka tulevat koodista.
 *
 * @param int $errno Virheen numero
 * @param String $errstr Virheviesti
 * @param String $errfile Tiedosto, jossa virhe tapahtui
 * @param int $errline Koodirivi, jossa virhe tapahtui
 * @throws \Exception
 * @return bool Mikäli koodissa heitetään E_DEPRECATED-virhe niin palautetaan true että homma jatkuu
 */
function my_error_handler($errno, $errstr, $errfile, $errline) {
    Akhj\Util\Logger::getInstance($errfile)->warn($errstr, $errline);
    // PHP 5.3.0 versiosta lähtien heitetään E_DEPRECATED-virhe jos käytetään php:n deprekoitua koodia
    // Tätä virhettä ei nyt käsitellä vaan annetaan homman jatkua, mutta logitetaan niin voidaan poistaa deprekoitu koodi
    if ($errno == 8192) {
        Akhj\Util\Logger::getInstance($errfile)->error("Poista deprekoitu koodi!", $errline);
        // Palautetaan true, jotta suoritus jatkuu
        return true;
    }
    // Heitetään virheestä poikkeus, joka käsitellään ExceptionHandler::handleException()-metodissa
    throw new \Exception("Virhe '$errstr' [$errno] tiedostossa '$errfile($errline)' ", $errno);
}
// Asetetaan oma virheidenkäsittelijä
set_error_handler("my_error_handler");


// Näytetään kaikki virheet jos APPLICATION_ENV on "development".
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

// Ladataan tarvittavat asetukset. Mikäli local.php löytyy niin käytetään sitä, muuten käytetään
// tuotantoympäristön asetuksia
if (file_exists(APPLICATION_DIR . '/inc/config/local.php')) {
    $appConfig = require (APPLICATION_DIR . '/inc/config/local.php');
} elseif (file_exists(APPLICATION_DIR . '/inc/config/production.php')) {
    $appConfig = require (APPLICATION_DIR . '/inc/config/production.php');
}

// Mikäli asetuksien haussa on jotain ongelmia, niin heitetään poikkeus ja lopetetaan tähän
if (!isset($appConfig) || !isset($appConfig["database"]) || !is_array($appConfig["database"])) {
    die ("Asetustiedostoa ei löydy!");
}

// Tallennetaan asetukset muistiin
\Akhj\Service\Storage::set(\Akhj\Service\Storage::KEY_APP_CONFIG, $appConfig);

/**
 * Doctrinen asetukset
 */
require 'doctrine-config.php';

