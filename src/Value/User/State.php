<?php

declare(strict_types=1);

namespace Del\Value\User;

use InvalidArgumentException;

class State
{
    const STATE_UNACTIVATED = 0;
    const STATE_ACTIVATED = 1;
    const STATE_DISABLED = 2;
    const STATE_SUSPENDED = 3;
    const STATE_BANNED = 4;

    const VALID_STATES = [
        self::STATE_ACTIVATED,
        self::STATE_BANNED,
        self::STATE_DISABLED,
        self::STATE_SUSPENDED,
        self::STATE_UNACTIVATED,
    ];

    private int $value;

    public function __construct(int $val)
    {
        if(!in_array($val, self::VALID_STATES)) {
            throw new InvalidArgumentException('Value is invalid');
        }

        $this->value = $val;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
