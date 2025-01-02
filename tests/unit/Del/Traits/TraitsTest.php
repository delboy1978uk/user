<?php

namespace Bone\Test\BoneDoctrine\Command;

use Codeception\Test\Unit;
use DateTime;
use Del\Entity\User;
use Del\Traits\HasApprovedBy;
use Del\Traits\HasDeletedBy;
use Del\Traits\HasOwnedBy;
use Del\Traits\HasRejectedBy;
use Del\Traits\HasUser;
use Doctrine\ORM\EntityManagerInterface;

class FakeClass
{
    use HasUser;
    use HasDeletedBy;
    use HasApprovedBy;
    use HasRejectedBy;
    use HasOwnedBy;
}

class TraitsTest extends Unit
{
    public function testTraits()
    {
        $user = new User();
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $class = new FakeClass();

        $class->setUser($user);
        $class->setDeletedBy($user);
        $class->setApprovedBy($user);
        $class->setRejectedBy($user);
        $class->setOwnedBy($user);

        self::assertInstanceOf(User::class, $class->getUser());
        self::assertInstanceOf(User::class, $class->getDeletedBy());
        self::assertInstanceOf(User::class, $class->getApprovedBy());
        self::assertInstanceOf(User::class, $class->getRejectedBy());
        self::assertInstanceOf(User::class, $class->getOwnedBy());

    }
}
