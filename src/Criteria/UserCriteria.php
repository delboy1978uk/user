<?php

declare(strict_types=1);

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

    protected ?int $id = null;
    protected ?string $email = null;
    protected ?int $state = null;
    protected ?string $registrationDate = null;
    protected ?string $lastLoginDate = null;
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected ?string $order = null;
    protected ?string $orderDirection = null;

    public function hasOffset(): bool
    {
        return $this->offset !== null;
    }

    public function setOffset(int $offset):  void
    {
        $this->offset = $offset;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function hasLimit(): bool
    {
        return $this->limit !== null;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function hasOrder(): bool
    {
        return $this->order !== null;
    }

    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }

    public function hasOrderDirection(): bool
    {
        return $this->orderDirection !== null;
    }

    public function setPagination(int  $page, int $limit): void
    {
        $offset = ($limit * $page) - $limit;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function hasId(): bool
    {
        return $this->id != null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function hasEmail(): bool
    {
        return $this->email != null;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function hasState(): bool
    {
        return $this->state != null;
    }

    public function getRegistrationDate(): string
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(string $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function hasRegistrationDate(): bool
    {
        return $this->registrationDate != null;
    }

    public function getLastLoginDate(): string
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(string $lastLoginDate): void
    {
        $this->lastLoginDate = $lastLoginDate;
    }

    public function hasLastLoginDate(): bool
    {
        return $this->lastLoginDate != null;
    }
}
