<?php

namespace Bone\Test\BoneDoctrine\Command;

use Bone\App\Traits\HasUser;
use Bone\BoneDoctrine\Traits\HasCreatedAtDate;
use Bone\BoneDoctrine\Traits\HasDeletedAtDate;
use Bone\BoneDoctrine\Traits\HasEmail;
use Bone\BoneDoctrine\Traits\HasEntityManagerTrait;
use Bone\BoneDoctrine\Traits\HasExpiryDate;
use Bone\BoneDoctrine\Traits\HasId;
use Bone\BoneDoctrine\Traits\HasImage;
use Bone\BoneDoctrine\Traits\HasName;
use Bone\BoneDoctrine\Traits\HasPrivacy;
use Bone\BoneDoctrine\Traits\HasSettings;
use Bone\BoneDoctrine\Traits\HasTelephone;
use Bone\BoneDoctrine\Traits\HasUpdatedAtDate;
use Bone\BoneDoctrine\Traits\HasURL;
use Bone\BoneDoctrine\Traits\HasURLSlug;
use Bone\BoneDoctrine\Traits\HasVisibility;
use Codeception\Test\Unit;
use DateTime;
use Del\Entity\User;
use Del\Traits\HasApprovedBy;
use Del\Traits\HasDeletedBy;
use Del\Traits\HasOwnedBy;
use Del\Traits\HasRejectedBy;
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
        self::assertInstanceOf(User::class, $class->getDeletedAt());
        self::assertInstanceOf(User::class, $class->getApprovedBy());
        self::assertInstanceOf(User::class, $class->getRejectedBy());
        self::assertInstanceOf(User::class, $class->getOwnedBy());

    }
}
