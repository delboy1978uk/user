<?php

namespace Del\Criteria;

use Del\Common\Criteria as CommonCriteria;


class UserCriteria extends CommonCriteria
{
    const ORDER_EMAIL_ASC        = 'email_asc';
    const ORDER_EMAIL_DESC       = 'email_desc';
    const ORDER_BEDROOMS            = 'numBedrooms';

    const ORDER_STATE_ASC        = 'state_asc';
    const ORDER_STATE_DESC       = 'state_desc';
    const ORDER_STATE            = 'state';

    const ORDER_REG_DATE_ASC        = 'registrationDate_asc';
    const ORDER_REG_DATE_DESC       = 'registrationDate_desc';
    const ORDER_REG_DATE            = 'registrationDate';

    const ORDER_LAST_LOGIN_DATE_ASC        = 'lastLoginDate_asc';
    const ORDER_LAST_LOGIN_DATE_DESC       = 'lastLoginDate_desc';
    const ORDER_LAST_LOGIN_DATE            = 'lastLoginDate';

    protected $id;
    protected $email;
    protected $state;
    protected $registrationDate;
    protected $lastLoginDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return $this->id != null;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserCriteria
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasEmail()
    {
        return $this->email != null;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     * @return UserCriteria
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasState()
    {
        return $this->state != null;
    }

    /**
     * @return mixed
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * @param mixed $registrationDate
     * @return UserCriteria
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasRegistrationDate()
    {
        return $this->registrationDate != null;
    }

    /**
     * @return mixed
     */
    public function getLastLoginDate()
    {
        return $this->lastLoginDate;
    }

    /**
     * @param mixed $lastLoginDate
     * @return UserCriteria
     */
    public function setLastLoginDate($lastLoginDate)
    {
        $this->lastLoginDate = $lastLoginDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasLastLoginDate()
    {
        return $this->lastLoginDate != null;
    }


}