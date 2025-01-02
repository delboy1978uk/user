<?php

declare(strict_types=1);

namespace Del\Traits;

use Del\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait HasDeletedBy
{
    #[ORM\ManyToOne]
    private ?User $deletedBy = null;

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }
}
