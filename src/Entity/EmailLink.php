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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EmailLink
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return EmailLink
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiry_date;
    }

    /**
     * @param DateTime $expiry_date
     * @return EmailLink
     */
    public function setExpiryDate(DateTime $expiry_date)
    {
        $this->expiry_date = $expiry_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return EmailLink
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
}


