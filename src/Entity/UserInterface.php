<?php

namespace Del\Entity;

use DateTime;
use Del\Person\Entity\Person;
use Del\Value\User\State;

interface UserInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return Person
     */
    public function getPerson();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @return State
     */
    public function getState();

    /**
     * @return DateTime
     */
    public function getRegistrationDate();

    /**
     * @return DateTime
     */
    public function getLastLoginDate();

    /**
     * @param $id
     * @return mixed
     */
    public function setID($id);
    
    /**
     * @param string $email
     * @return UserInterface
     */
    public function setEmail($email);

    /**
     * @param Person $person
     * @return UserInterface
     */
    public function setPerson(Person $person);

    /**
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password);

    /**
     * @param State $state
     * @return UserInterface
     */
    public function setState(State $state);

    /**
     * @param DateTime $registrationDate
     * @return UserInterface
     */
    public function setRegistrationDate($registrationDate);

    /**
     * @param DateTime $lastLogin
     * @return UserInterface
     */
    public function setLastLogin(DateTime $lastLogin);
}