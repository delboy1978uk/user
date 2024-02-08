<?php

declare(strict_types=1);

namespace Del\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'Del\Repository\EmailLink')]
class EmailLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', length: 11)]
    private $id;

    #[ORM\ManyToOne(targetEntity: 'Del\Entity\User', cascade: ['persist'])]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $expiryDate;

    #[ORM\Column(type: 'string')]
    private $token;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getExpiryDate(): DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(DateTimeInterface $expiry_date): void
    {
        $this->expiryDate = $expiry_date;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token):  void
    {
        $this->token = $token;
    }
}


