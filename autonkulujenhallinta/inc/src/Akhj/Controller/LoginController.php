<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Controller;

use Akhj\Service\UserService;
use Akhj\Util\Logger;
use Akhj\View;

/**
 * Controlleri joka hoitaa loginin ja logoutin
 *
 * @package Akhj\Controller
 * @author Mikko Rainio
 */
class LoginController extends AbstractBaseController
{

    /**
     * Oletusaction. Tarjoaa kirjautumisruudun
     */
    public function indexAction()
    {
        $this->logger->debug("indexAction");
        
        // Jos ollaan jo kirjauduttu, niin ohjataan etusivulle
        if (UserService::hasIdentity()) {
            $this->redirect("index");
        }
        
        $view = new View('login.phtml');

        if (isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['password'])) {
            
            $this->logger->debug("Tarkistetaan lomakedata");
            $this->logger->debug($_POST);            
            
            // Tarkistetaan tunnus ja salasana
            UserService::identify($_POST['email'], $_POST['password']);
            
            // Mikäli nyt ollaan kirjauduttu, niin ohjataan etusivulle
            if (UserService::hasIdentity()) {
                $this->logger->debug("Kirjautuminen onnistui!");
                
                $this->redirect("index");
            } else {
                // Muussa tapauksessa näytetään käyttäjälle virheviesti
                $view->assign("error", "Käyttäjätunnus tai salasana ei täsmää!");
            }            
        }

        $view->render();
        
    }
    
    /**
     * Action, jolla kirjaudutaan ulos. Uloskirjautumisen jälkeen ohjataan käyttäjä kirjautumisruutuun
     */
    public function logoutAction() {
        
        $this->logger->debug("logoutAction");
        
        UserService::destroyIdentity();
        
        $this->redirect("index");
    }

    /**
     * Controllerin initialisointimetodi. 
     */
    protected function init()
    {
        $this->logger = Logger::getInstance(__FILE__);
    }

}
