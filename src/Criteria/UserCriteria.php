<?php

namespace Del\Criteria;

class UserCriteria
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

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

    protected $limit;
    protected $offset;
    protected $order;
    protected $orderDirection;

    /**
     * @return bool
     */
    public function hasOffset()
    {
        return $this->offset !== null;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setOffset($code)
    {
        $this->offset = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        return $this->limit !== null;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setLimit($code)
    {
        $this->limit = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function hasOrder()
    {
        return $this->order !== null;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return mixed
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @param mixed $orderDirection
     * @return Criteria
     */
    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrderDirection()
    {
        return $this->orderDirection !== null;
    }

    /**
     * @param $page
     * @param $limit
     */
    public function setPagination($page, $limit)
    {
        $offset = ($limit * $page) - $limit;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

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
     * @return string
     */
    public function getRegistrationDate(): string
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
     * @return string
     */
    public function getLastLoginDate(): string
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
