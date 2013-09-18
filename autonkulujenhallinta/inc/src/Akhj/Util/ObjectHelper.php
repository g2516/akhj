<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Util;
/**
 * Luokkien hallinnoimisen apumetodeja
 *
 * @package Akhj\Util
 * @author Mikko Rainio
 */
class ObjectHelper {

    /**
     * Täyttää dto:on arvot arraysta
     * 
     * @param array $array              Täytettävät arvot
     * @param object $object            Täytettävä olio
     * @param boolean $lisaaMyosTyhjat  Jos tämä on TRUE niin täytetään myös NULL-arvot. Oletus = FALSE
     * @return object
     * @throws \InvalidArgumentException mikäli $array ei ole taulukko
     */
    public static function taytaOlio($array, $object, $lisaaMyosTyhjat = false) {
        if (!is_array($array)) {
            throw new \InvalidArgumentException("Ensimmäinen parametri pitää olla taulukko!");
        }
        if (count($array) == 1 && is_array($array[0])) {
            return ObjectHelper::taytaOlio($array[0], $object);
        }
        $metodit = get_class_methods(get_class($object));
        foreach ($metodit as $metodi) {
            if (substr($metodi,0,3) == 'set') {
                $var = StringHelper::strToLower(str_replace(substr($metodi,0,3), "", $metodi));
                if (array_key_exists($var, $array) && ((!is_null($array[$var]) && $array[$var] != '') || $lisaaMyosTyhjat)) {
                    $object->$metodi($array[$var]);
                }
            }
        }
        return $object;
    }
}
?>
