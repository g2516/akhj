<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */
namespace Akhj\Exception;
/**
 * Custom-ecxeptionien yläluokka helpottamaan logitusta.
 *
 * @author Mikko Rainio
 */
class BaseException extends \ErrorException {

    /**
     * Virhekoodi joka ilmaisee että config-tiedostossa tai -hakemistossa on ongelma
     * @var int
     */
    const DISABLE_LOGGING = 100000;

    /**
     * Ylikirjoitetaan __toString()-metodi jotta saadaan luettavampaa tietoa mm. logitusta varten
     * @return string
     */
    public function __toString() {
        return print_r($this, true);
    }
}
