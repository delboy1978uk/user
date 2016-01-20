<?php

namespace Del\Entity;

/**
 * @Entity(repositoryClass="Del\Repository\EmailLink")
 */
class EmailLink
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     *  @ManyToOne(targetEntity="Del\Entity\User")
     */
    private $user;

    /**
     * @Column(type="datetime")
     */
    private $expiry_date;

    /**
     * @Column(type="string")
     */
    private $token;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return EmailLink
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return EmailLink
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiryDate()
    {
        return $this->expiry_date;
    }

    /**
     * @param mixed $expiry_date
     * @return EmailLink
     */
    public function setExpiryDate($expiry_date)
    {
        $this->expiry_date = $expiry_date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return EmailLink
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }


}


