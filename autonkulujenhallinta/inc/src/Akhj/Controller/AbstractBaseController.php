<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Controller;

use Akhj\Exception\ActionNotFoundException;
use Akhj\Exception\ControllerNotSetException;
use Akhj\Exception\ExceptionHandler;
use Akhj\Util\StringHelper;
use Akhj\Util\Logger;

/**
 * AbstractBaseController on kaikkien controllereiden yläluokka.
 *
 * @package Akhj\Controller
 * @author Mikko Rainio
 */
abstract class AbstractBaseController {

    /**
     * Tämän controllerin nimi
     * @var String
     */
    protected $nimi;

    /**
     * Loggeri
     * @var Logger
     */
    protected $logger;

    /**
     * Konstruktori. Älä ylikirjoita, käytä init()-metodia.
     * @param String $nimi Controllerin nimi
     */
    public function __construct($nimi) {
        $this->nimi = $nimi;
        $this->init();
    }

    /**
     * Controllerin initialisointimetodi. Tätä kutsutaan constructorista.
     * @abstract
     */
    abstract protected function init();

    /**
     * Oletusactioni
     * @abstract
     */
    abstract public function indexAction();

    /**
     * Suorittaa pyydetyn actionin, mikäli se on olemassa.
     * @param String $action Suoritettava action
     * @throws ActionNotFoundException Mikäli pyydettyä actionia ei ole
     * @throws \Exception Mikäli yritettiin ajaa actioneita joita ei saa ajaa
     */
    public function execute($action) {
        $actionName = $action . 'Action';
        if(method_exists($this, $actionName)) {
            $method = new \ReflectionMethod(get_class($this), $actionName);
            $reserved = array('__construct', 'execute', 'getView');
            if(in_array($actionName, $reserved) || $method->isProtected() || $method->isPrivate() || $method->isStatic()) {
                $this->logger->error('Tried to execute reserved action '.$action.' in '.$this->nimi);
                throw new \Exception('Tried to execute reserved action '.$action.' in '.$this->nimi);
            }
            
            if ($this->securityCheck($action)) {
                try {
                    $this->$actionName();
                } catch (\Exception $ex) {
                    ExceptionHandler::handleException($ex);
                }   
            } else {
                $this->logger->error("SecurityCheck ei mennyt läpi! action=$action");
                $this->redirect("index");
            }
        } else {
            throw new ActionNotFoundException($this->nimi.' does not support action '.$action);
        }
    }

    /**
     * Tarkistaa onko requestilla tähän controlleriin ja actioniin oikeutta
     * 
     * @param String $action Pyydetty action
     * @return boolean Palauttaa tiedon että saako pyydettyä actionia näyttää
     */
    protected function securityCheck($action) {
        return true;
    }

    /**
     * Uudelleenohjaa pyynnön annettuun controlleriin ja actioniin.
     *
     * Suoritus pysähtyy tämän metodin lopussa ja uudelleenohjaus tapahtuu välittömästi.
     * Esimerkki:
     * <code>
     * $this->redirect('jokukontrolleri', 'muokkaus', array("id" => $id, "toinen_id" => $toinen_id));
     * </code>
     * 
     * @param String $controller    Kohdecontrolleri
     * @param String $action        Kohdeaction, oletuksena 'index'
     * @param array $arglist        Välitettävät parametrit
     * @param String $anchor        Linkkiin lisättävä ankkuri
     * @throws ControllerNotSetException Mikäli controlleria ei ole annettu
     */
    public function redirect($controller, $action="index", $arglist=array(), $anchor=null) {
        if (StringHelper::isBlank($controller)) {
            throw new ControllerNotSetException("Uudelleenohjaukseen pitää määrittää kohdecontrolleri");
        }
        if (is_array($action)) {
            if ($arglist != null && !is_array($arglist) && is_string($arglist)) {
                $anchor = $arglist;
            }
            $arglist = $action;
            $action = "index";
        }
        $url = $_SERVER['SCRIPT_NAME'] . "?controller=$controller";
        if ($action !== "index") {
            $url .= "&action=$action";
        }
        if ($arglist !== NULL && count($arglist) != 0) {
            foreach ($arglist as $key => $value) {
                if ($key !== "controller" && $key !== "action") {
                    $url .= "&$key=$value";
                }
            }
        }
        if (!StringHelper::isBlank($anchor)) {
            $url .= '#' . $anchor;
        }
        header("Location: " . $url);
        die();
    }

}
