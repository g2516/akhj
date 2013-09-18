<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 * Tämän sivun kautta kuljetaan kaikkialle. Parametreista päätellään, että
 * mitä controlleria ja mitä actoinia on kutsuttu.
 *
 * @author Mikko Rainio
 */

use Akhj\Controller\ControllerFactory;
use Akhj\Service\UserService;

require_once('../bootstrap.php');

if (!UserService::hasIdentity()) {
    $page = "login";
    $action = "index";
} else {
    $page = isset($_REQUEST['controller']) && is_string($_REQUEST['controller']) ? $_REQUEST['controller'] : 'index';
    $action = isset($_REQUEST['action']) && is_string($_REQUEST['action']) ? $_REQUEST['action'] : 'index';
}

// ControllerFactory luo pyydetyn controllerin ja sen jälkeen suoritetaan haluttu action.
// Mikäli controlleria tai actionia ei löydy, niin siitä aiheutuu virhe, jonka ExceptionHandler hoitaa ja esittää virhesivun
ControllerFactory::create($page)->execute($action);
