<?php

namespace Del\Entity;

use DateTime;
use Del\Value\User\State;
use Del\Person\Entity\Person;

/**
 * @MappedSuperclass()
 */
class BaseUser implements UserInterface
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
     * @OneToOne(targetEntity="Del\Person\Entity\Person",cascade="persist")
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

    /**
     * BaseUser constructor.
     */
    public function __construct()
    {
        $this->state = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
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

    /**
     * @return string
     */
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

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param Person $person
     * @return $this|UserInterface
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;
        return $this;
    }

    /**
     * @param string $password
     * @return $this|UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param State $state
     * @return $this
     */
    public function setState(State $state)
    {
        $this->state = $state->getValue();
        return $this;
    }

    /**
     * @param DateTime $registrationDate
     * @return $this
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @param DateTime $lastLogin
     * @return $this
     */
    public function setLastLogin(DateTime $lastLogin)
    {
        $this->lastLoginDate = $lastLogin;
        return $this;
    }
}