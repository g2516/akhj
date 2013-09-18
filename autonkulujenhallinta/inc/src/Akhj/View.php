<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj;

/**
 * View-luokka sivujen parsimiseen
 *
 * @author Mikko Rainio
 */
class View
{

    /**
     * Pääsivupohjan nimi
     * @var String
     */
    private $border = 'layout.phtml';

    /**
     * Polku tämän viewin templateen
     * @var String
     */
    private $template;

    /**
     * Templatelle välitettävät muuttujat
     * @var array
     */
    private $vars = array();

    /**
     * Luokan loggeri
     * 
     * @var Util\Logger
     */
    private $logger = null;

    /**
     * Luo uuden näkymän
     *
     * @param String $templatenNimi Templaten tiedostonimi
     * @throws \Exception
     */
    public function __construct($templatenNimi)
    {
        $this->logger = Util\Logger::getInstance(__FILE__);
        $this->template = TEMPLATES_DIR . '/' . $templatenNimi;
        if (!file_exists($this->template)) {
            throw new \Exception('Templatea ' . $this->template . ' ei löydy.');
        }
    }

    /**
     * Asettaa templatelle muuttujan
     *
     * @param string $name variable name
     * @param mixed $value variable value
     * @return View
     */
    public final function assign($name, $value)
    {
        $this->vars[$name] = $value;
        return $this;
    }

    /**
     * Asettaa viewissä käytettävän layout-pohjan
     * @param $layoutTemplateName
     */
    public final function setBorder($layoutTemplateName)
    {
        $this->border = $layoutTemplateName;
    }

    /**
     * Renderöi templaten
     */
    public final function render()
    {
        extract($this->vars);
        /** Sivun runkotiedosto */
        include TEMPLATES_DIR . '/' . $this->border;
    }

    /**
     * Muodostaa urlin annettuun controlleriin ja actioniin.
     *
     * @param String $controller Controlleri
     * @param String $action     Actioni
     * @param array $arglist     Taulukko urliin liitettävistä parametreista
     * @param String $anchor     Linkkiin lisättävä ankkuri
     * @return String Url
     */
    public final function urlHelper($controller, $action = "index", array $arglist = array(), $anchor = null)
    {
        $url = $_SERVER['SCRIPT_NAME'] . "?controller=$controller";
        if ($action !== "index") {
            $url .= "&action=$action";
        }

        if ($arglist != null && count($arglist) > 0) {
            foreach ($arglist as $key => $value)
            {
                if ($key !== "controller" && $key !== "action") {
                    if (is_array($value)) {
                        foreach ($value as $val)
                        {
                            $url .= '&' . $key . '[]=' . $val;
                        }
                    } else {
                        $url .= "&$key=$value";
                    }
                }
            }
        }
        if (!Util\StringHelper::isBlank($anchor)) {
            $url .= '#' . $anchor;
        }
        return $url;
    }

    /**
     * Palauttaa näkymän template-tiedoston nimen jos se on asetettu
     * @return String Template-tiedoston nimi
     */
    public function getTemplate()
    {
        return $this->template;
    }

}
