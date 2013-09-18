<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Exception;

use Akhj\Util\Logger;
use Akhj\View;

/**
 * Poikkeusten käsittelijä joka näyttää virhesivun jos kiinniottamaton poikkeus
 * pääsee valloilleen.
 *
 * @author Mikko Rainio
 */
class ExceptionHandler {
    public function __construct() {}

    /**
     * Näyttää virhesivun ja lopettaa suorituksen 
     * @param \Exception $e Heitetty poikkeus
     */
    public static function printException(\Exception $e) {

        try {
            $view = new View('error.phtml');

            $view->assign("e", $e);
            $view->render();
        } catch (\Exception $e)
        {
            if (defined(DEVELOPMENT_MODE) && DEVELOPMENT_MODE) {
                die( get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine() );
            } else {
                die ("Järjestelmävirhe, yritä myöhemmin uudelleen.");
            }
        }
    }

    /**
     * Staattinen metodi jota voi käyttää automaattisena poikkeuksien käsittelijänä.
     *
     * @see set_exception_handler()
     * @param \Exception $e Heitetty poikkeus
     */
    public static function handleException(\Exception $e) {

        Logger::getInstance(__FILE__)->error($e);
        Logger::getInstance(__FILE__)->error($_REQUEST);
        
        self::printException($e);
    }
}