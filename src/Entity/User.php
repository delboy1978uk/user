<?php

declare(strict_types=1);

namespace Del\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'Del\Repository\UserRepository')]
#[ORM\Table(name: 'User')]
#[ORM\UniqueConstraint(name: 'email_idx', columns: ['email'])]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
class User extends BaseUser
{

}
