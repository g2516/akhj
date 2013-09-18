<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 * Tiedosto sisältää Doctrinen konfiguraatiot.
 *
 * @author Mikko Rainio
 */

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Akhj\Service\Storage;

$appConfig = Storage::get( Storage::KEY_APP_CONFIG );
        
// Entity-luokkien polku
$paths = array("inc/src/Akhj/Entity");

// the connection configuration
$dbParams = $appConfig["database"];

$proxyDir = APPLICATION_DIR . "/inc/src/Akhj/Entity/Proxy";

$config = Setup::createAnnotationMetadataConfiguration($paths, DEVELOPMENT_MODE, $proxyDir);
$entityManager = EntityManager::create($dbParams, $config);

// Tallennetaan entitymanager
Storage::set(Storage::KEY_ENTITY_MANAGER, $entityManager);