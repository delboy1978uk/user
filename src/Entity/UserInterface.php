<?php

declare(strict_types=1);

namespace Del\Entity;

use DateTimeInterface;
use Del\Person\Entity\Person;
use Del\Value\User\State;

interface UserInterface
{
    public function getId(): ?int;
    public function getEmail(): string;
    public function getPerson(): Person;
    public function getPassword(): string;
    public function getState(): State;
    public function getRegistrationDate(): DateTimeInterface;
    public function getLastLoginDate(): ?DateTimeInterface;
    public function setId(int $id): void;
    public function setEmail(string $email): void;
    public function setPerson(Person $person): void;
    public function setPassword(string $password): void;
    public function setState(State $state): void;
    public function setRegistrationDate(DateTimeInterface $registrationDate): void;
    public function setLastLogin(DateTimeInterface $lastLogin): void ;
}
