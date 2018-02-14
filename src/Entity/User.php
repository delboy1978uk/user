<?php

namespace Del\Entity;

/**
 * Class User
 * @package Del\Entity
 * @Entity(repositoryClass="Del\Repository\UserRepository")
 * @Table(name="User",uniqueConstraints={@UniqueConstraint(name="email_idx", columns={"email"})})
 */
class User extends BaseUser
{

}