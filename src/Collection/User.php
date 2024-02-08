<?php

declare(strict_types=1);

namespace Del\Collection;

use Del\Entity\User as UserEntity;
use Doctrine\Common\Collections\ArrayCollection;
use LogicException;

class User extends ArrayCollection
{
    public function update(UserEntity $user): void
    {
        $key = $this->findKey($user);

        if($key) {
            $this->offsetSet($key,$user);
        }

        throw new LogicException('User was not in the collection.');
    }

    public function append(UserEntity $user): void
    {
        $this->add($user);
    }

    public function current(): ?UserEntity
    {
        return parent::current();
    }

    public function findKey(UserEntity $user): bool|int
    {
        $it = $this->getIterator();
        $it->rewind();

        while($it->valid()) {
            if($it->current()->getId() == $user->getId()) {
                return $it->key();
            }
            $it->next();
        }

        return false;
    }

    public function findById($id): UserEntity|bool
    {
        $it = $this->getIterator();
        $it->rewind();

        while($it->valid()) {
            if($it->current()->getId() == $id) {
                return $it->current();
            }

            $it->next();
        }

        return false;
    }

}
