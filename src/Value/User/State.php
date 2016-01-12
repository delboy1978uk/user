<?php

namespace Del\Value\User;

use InvalidArgumentException;

class State
{
    const STATE_UNACTIVATED = 0;
    const STATE_ACTIVATED = 1;
    const STATE_DISABLED = 2;
    const STATE_SUSPENDED = 3;
    const STATE_BANNED = 4;

    private $value;

    /**
     * @param $val
     */
    public function __construct($val)
    {
        if(!in_array($val, [self::STATE_UNACTIVATED, self::STATE_ACTIVATED, self::STATE_DISABLED, self::STATE_SUSPENDED, self::STATE_BANNED])) {
            throw new InvalidArgumentException('Value is invalid');
        }
        $this->value = $val;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}