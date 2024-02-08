<?php

declare(strict_types=1);

namespace Del\Entity;

use DateTimeInterface;
use Del\Value\User\State;
use Del\Person\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
class BaseUser implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $email;

    #[ORM\OneToOne(targetEntity: 'Del\Person\Entity\Person',cascade: ['persist'])]
    private $person;

    #[ORM\Column(type: 'string', length: 100)]
    private $password;

    #[ORM\Column(type: 'integer', length: 1)]
    private int $state;

    #[ORM\Column(type: 'datetime', nullable:true)]
    private ?DateTime $registrationDate = null;

    #[ORM\Column(type: 'datetime', nullable:  true)]
    private ?DateTime $lastLoginDate = null;

    public function __construct()
    {
        $this->state = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getState(): State
    {
        return new State($this->state);
    }

    public function getRegistrationDate(): DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function getLastLoginDate(): ?DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setState(State $state): void
    {
        $this->state = $state->getValue();
    }

    public function setRegistrationDate(DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function setLastLogin(DateTimeInterface $lastLogin): void
    {
        $this->lastLoginDate = $lastLogin;
    }
}
