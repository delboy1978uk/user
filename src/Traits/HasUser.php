<?php

declare(strict_types=1);

namespace Del\Traits;

use Del\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait HasUser
{
    #[ORM\ManyToOne]
    private User $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
