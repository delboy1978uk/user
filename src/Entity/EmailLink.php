<?php

namespace Del\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Del\Repository\EmailLink")
 */
class EmailLink
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     *  @ORM\ManyToOne(targetEntity="Del\Entity\User",cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiry_date;

    /**
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return EmailLink
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getExpiryDate(): DateTime
    {
        return $this->expiry_date;
    }

    /**
     * @param DateTime $expiry_date
     * @return EmailLink
     */
    public function setExpiryDate(DateTime $expiry_date): void
    {
        $this->expiry_date = $expiry_date;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return EmailLink
     */
    public function setToken(string $token):  void
    {
        $this->token = $token;
    }
}


