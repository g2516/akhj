<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Entity;

/**
 * Class User
 *
 * @package Akhj\Entity
 * @Entity(repositoryClass="Akhj\Entity\Repository\UserRepository")
 * @HasLifecycleCallbacks
 */
class User {

    /**
     * Käyttäjän yksilöllinen tunnus
     *
     * @Id()
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * Käyttäjän sähköpostiosoite. Käytetään myös kirjautumisessa.
     *
     * @Column(type="string", length=255, unique=true)
     * @var string
     */
    private $email;

    /**
     * Käyttäjän etunimi
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $firstname;

    /**
     * Käyttäjän sukunimi
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $lastname;

    /**
     * Käyttäjän salasana.
     *
     * @Column(type="string", length=255)
     * @var string
     */
    private $password;

    /**
     * Käyttäjän luontipäivämäärä
     *
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $created = null;

    /**
     * Käyttäjän tietojen muokkauspäivämäärä
     *
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $updated = null;

    /**
     * Doctrinen lifecycle-metodi joka päivittää entityn päivämäärät kun se persistoidaan.
     *
     * @PrePersist
     * @PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdated(new \DateTime('now'));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime('now'));
        }
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setFirstname($name)
    {
        $this->firstname = $name;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * 
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * 
     * @param string $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

}