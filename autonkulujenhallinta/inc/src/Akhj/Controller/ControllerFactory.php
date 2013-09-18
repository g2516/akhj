<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Controller;

use Akhj\Exception\ControllerNotFoundException;
use Akhj\Util\StringHelper;

/**
 * ControllerFactory controllerien dynaamiseen lataamiseen
 *
 * @package Akhj\Controller
 * @author Mikko Rainio
 */
class ControllerFactory {

    /**
     * Muodostaa Controllerin
     *
     * @param String $controller
     * @throws \Akhj\Exception\ControllerNotFoundException
     * @return AbstractBaseController Controller
     */
    public static function create($controller) {
        $cn = __NAMESPACE__ . '\\' . StringHelper::ucFirst($controller) . 'Controller';

        try {
            if (class_exists($cn)) { 
                $c = new $cn($controller);
                return $c;
            }
        } catch (\Exception $e) { }
        throw new ControllerNotFoundException("Controlleria '$controller' ei voitu ladata!");
    }
}