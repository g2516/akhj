<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Controller;

use Akhj\Service\Storage;
use Akhj\Service\UserService;
use Akhj\Util\Logger;
use Akhj\Util\ObjectHelper;
use Akhj\View;

/**
 * Etusivun controlleri
 *
 * @package Akhj\Controller
 * @author Mikko Rainio
 */
class IndexController extends AbstractBaseController
{

    /**
     * Oletusaction
     */
    public function indexAction()
    {
        $this->logger->debug("indexAction");

        $view = new View('frontpage/index.phtml');

        $view->assign("user", UserService::getIdentity());
        $view->assign("title", "Etusivu");
        $view->render();
    }
    
    /**
     * Esimerkkiaction jossa päivitetään käyttäjän tiedot. Puuttuu esim virheidenkäsittely.
     */
    public function editAction() {
        if (isset($_POST) && isset($_POST['id'])) {
            // Otetaan entityManager käyttöön
            $em = Storage::get(Storage::KEY_ENTITY_MANAGER);
            
            // Täytetään sessiossa oleva User-olio formin datalla
            $user = ObjectHelper::taytaOlio($_POST, UserService::getIdentity());
            
            // Tallennetaan muokatut tiedot
            $em->flush();
            
            // Tallennetaan muokattu olio sessioon
            UserService::storeIdentity($user);
        }
        
        
        $this->redirect("index");
    }

    /**
     * Controllerin initialisointimetodi. Hyvä paikka esim alustaa loggeri
     */
    protected function init()
    {
        $this->logger = Logger::getInstance(__FILE__);
    }

}
