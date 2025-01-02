<?php

declare(strict_types=1);

namespace Del\Traits;

use Del\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait HasApprovedBy
{
    #[ORM\ManyToOne]
    private ?User $approvedBy = null;

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }
}
