<?php

namespace Del\Criteria;

use DateTime;
use Del\Common\Criteria\AbstractCriteria;

class UserCriteria extends AbstractCriteria
{
    const ORDER_ID = 'id';
    const ORDER_EMAIL = 'email';
    const ORDER_STATE  = 'state';
    const ORDER_REG_DATE = 'registrationDate';
    const ORDER_LAST_LOGIN_DATE = 'lastLoginDate';

    protected $id;
    protected $email;
    protected $state;
    protected $registrationDate;
    protected $lastLoginDate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return $this->id != null;
    }

    /**
     * @return mixed
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function hasEmail(): bool
    {
        return $this->email != null;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState(int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return bool
     */
    public function hasState(): bool
    {
        return $this->state != null;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param string $registrationDate
     */
    public function setRegistrationDate(string $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return bool
     */
    public function hasRegistrationDate(): bool
    {
        return $this->registrationDate != null;
    }

    /**
     * @return DateTime
     */
    public function getLastLoginDate(): DateTime
    {
        return $this->lastLoginDate;
    }

    /**
     * @param string $lastLoginDate
     */
    public function setLastLoginDate(string $lastLoginDate): void
    {
        $this->lastLoginDate = $lastLoginDate;
    }

    /**
     * @return bool
     */
    public function hasLastLoginDate(): bool
    {
        return $this->lastLoginDate != null;
    }
}
