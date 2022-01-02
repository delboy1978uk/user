<?php

namespace Del\Entity;

use DateTime;
use Del\Value\User\State;
use Del\Person\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=50)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="Del\Person\Entity\Person",cascade={"persist"})
     */
    private $person;

    /** @ORM\Column(type="string",length=100) */
    private $password;

    /**
     * @ORM\Column(type="integer",length=1)
     * @var int
     */
    private $state;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @var DateTime
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="datetime",nullable=true)
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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return new State($this->state);
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @return DateTime
     */
    public function getLastLoginDate(): ?DateTime
    {
        return $this->lastLoginDate;
    }

    /**
     * @param $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param mixed $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param State $state
     */
    public function setState(State $state): void
    {
        $this->state = $state->getValue();
    }

    /**
     * @param DateTime $registrationDate
     */
    public function setRegistrationDate(DateTime $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @param DateTime $lastLogin
     */
    public function setLastLogin(DateTime $lastLogin): void
    {
        $this->lastLoginDate = $lastLogin;
    }
}
