<?php

namespace Del\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package Del\Entity
 * @ORM\Entity(repositoryClass="Del\Repository\UserRepository")
 * @ORM\Table(name="User",uniqueConstraints={@ORM\UniqueConstraint(name="email_idx", columns={"email"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="class", type="string")
 */
class User extends BaseUser
{

}
