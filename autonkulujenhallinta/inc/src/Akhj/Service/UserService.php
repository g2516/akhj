<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Service;

use Akhj\Entity\User;
use Akhj\Security\PBKDF2;

/**
 * Class UserService
 *
 * @package Akhj\Service
 */
class UserService {
    
    const SESSION_KEY_USER = "session_key_user";
            
    /**
     * Palauttaa tiedon onko käyttäjä kirjautunut
     * 
     * @return boolean
     */
    public static function hasIdentity() {
        return isset($_SESSION[UserService::SESSION_KEY_USER]) && $_SESSION[UserService::SESSION_KEY_USER] instanceof User;
    }
    
    /**
     * Tallentaa käyttäjän tiedot sessioon
     * 
     * @param User $user
     */
    public static function storeIdentity(User $user) {
        $em = Storage::get(Storage::KEY_ENTITY_MANAGER);
        $em->detach($user);
        $_SESSION[UserService::SESSION_KEY_USER] = $user;
    }
    
    /**
     * Palauttaa tallennetun käyttäjän tiedot tai null jos ei löydy
     * @return User
     */
    public static function getIdentity() {
        if (self::hasIdentity()) {
            $user = $_SESSION[UserService::SESSION_KEY_USER];        
            $em = Storage::get(Storage::KEY_ENTITY_MANAGER);
            return $em->merge($user);
        } else {
            return null;
        }
    }
    
    /**
     * Tuhoaa olemassaolevat tiedot sessiosta
     */
    public static function destroyIdentity() {
        if (isset($_SESSION[UserService::SESSION_KEY_USER]))
            unset($_SESSION[UserService::SESSION_KEY_USER]);
        session_destroy();
    }
    
    /**
     * Yrittää hakea tietokannasta käyttäjän jolla on annettu email ja salasana.
     * Onnistuessaan palauttaa löydetyn käyttäjän ja tallentaa sen myös sessioon.
     * Mikäli käyttäjän tiedot on jo haettu, niin vanhat tiedot tuhotaan sessiosta 
     * vaikka käyttäjää ei löytyisikään tietokannasta.
     * 
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public static function identify($email, $password) {
        if (isset($_SESSION[UserService::SESSION_KEY_USER]))
            unset($_SESSION[UserService::SESSION_KEY_USER]);
        
        $em = Storage::get(Storage::KEY_ENTITY_MANAGER);
        $user = $em->getRepository("Akhj\Entity\User")->findOneBy(array(
            "email" => $email
        ));
        if ($user !== null && $user instanceof User) {
            if (PBKDF2::validatePassword($password, $user->getPassword())) {
                self::storeIdentity($user);
                return self::getIdentity();
            }
        }
        
        return null;
        
    }
}