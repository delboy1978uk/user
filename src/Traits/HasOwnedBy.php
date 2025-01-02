<?php

declare(strict_types=1);

namespace Del\Traits;

use Del\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait HasOwnedBy
{
    #[ORM\ManyToOne]
    private ?User $ownedBy = null;

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): void
    {
        $this->ownedBy = $ownedBy;
    }
}
