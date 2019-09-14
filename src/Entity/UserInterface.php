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
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return Person
     */
    public function getPerson(): Person;

    /**
     * @return string
     */
    public function getPassword(): string ;

    /**
     * @return State
     */
    public function getState(): State;

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime;

    /**
     * @return DateTime|null
     */
    public function getLastLoginDate(): ?DateTime;

    /**
     * @param $id
     */
    public function setId(int $id): void;
    
    /**
     * @param string $email
     */
    public function setEmail(string $email): void;

    /**
     * @param Person $person
     */
    public function setPerson(Person $person): void;

    /**
     * @param string $password
     */
    public function setPassword(string $password): void;

    /**
     * @param State $state
     */
    public function setState(State $state): void ;

    /**
     * @param DateTime $registrationDate
     */
    public function setRegistrationDate(DateTime $registrationDate): void;

    /**
     * @param DateTime $lastLogin
     */
    public function setLastLogin(DateTime $lastLogin): void ;
}
