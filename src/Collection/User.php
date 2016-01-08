<?php

namespace Del\Collection;

use Del\Entity\User as UserEntity;
use Doctrine\Common\Collections\ArrayCollection;
use LogicException;

class User extends ArrayCollection
{
    /**
     * @param UserEntity $user
     * @return $this
     */
    public function update(UserEntity $user)
    {
        $key = $this->findKey($user);
        if($key) {

            $this->offsetSet($key,$user);
            return $this;

        }
        throw new LogicException('User was not in the collection.');
    }

    /**
     * @param UserEntity $user
     */
    public function append(UserEntity $user)
    {
        $this->add($user);
    }

    /**
     * @return UserEntity|null
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * @param UserEntity $user
     * @return bool|int
     */
    public function findKey(UserEntity $user)
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



    public function findById($id)
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