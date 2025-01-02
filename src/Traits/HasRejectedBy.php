<?php

declare(strict_types=1);

namespace Del\Traits;

use Del\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait HasRejectedBy
{
    #[ORM\ManyToOne]
    private ?User $rejectedBy = null;

    public function getRejectedBy(): ?User
    {
        return $this->rejectedBy;
    }

    public function setRejectedBy(?User $rejectedBy): void
    {
        $this->rejectedBy = $rejectedBy;
    }
}
