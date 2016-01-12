<?php

namespace Del\Entity;

use DateTime;
use Del\Entity\Person;
use Del\Value\User\State;

/**
 * @Entity(repositoryClass="Del\Repository\User")
 */
class User
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string",length=50)
     */
    private $email;

    /**
     * @OneToOne(targetEntity="Del\Entity\Person",inversedBy="user")
     */
    private $person;

    /** @Column(type="string",length=100) */
    private $password;

    /**
     * @Column(type="integer",length=1)
     * @var int
     */
    private $state;

    /**
     * @Column(type="date",nullable=true)
     * @var DateTime
     */
    private $registrationDate;

    /**
     * @Column(type="date",nullable=true)
     * @var DateTime
     */
    private $lastLoginDate;

    public function __construct()
    {
        $this->state = 0;
    }


    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }


    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return new State($this->state);
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @return DateTime
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPerson(Person $person)
    {
        $this->person = $person;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param State $state
     * @return User
     */
    public function setState(State $state)
    {
        $this->state = $state->getValue();
        return $this;
    }

    /**
     * @param DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @param DateTime $lastLogin
     * @return User
     */
    public function setLastLogin(DateTime $lastLogin)
    {
        $this->lastLoginDate = $lastLogin;
        return $this;
    }
}