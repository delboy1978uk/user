<?php

declare(strict_types=1);

namespace Del\Entity;

use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use Bone\BoneDoctrine\Traits\HasEmail;
use Bone\BoneDoctrine\Traits\HasId;
use Bone\BoneDoctrine\Traits\HasPassword;
use DateTimeInterface;
use Del\Form\Field\Attributes\Field;
use Del\Form\Field\Transformer\DateTimeTransformer;
use Del\Form\Traits\HasFormFields;
use Del\Person\Traits\HasOneToOnePerson;
use Del\Value\User\State;
use Del\Person\Entity\Person;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'Del\Repository\UserRepository')]
#[ORM\Table(name: 'User')]
#[ORM\UniqueConstraint(name: 'email_idx', columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    use HasFormFields;
    use HasId;
    use HasEmail;
    use HasOneToOnePerson;
    use HasPassword;

    #[ORM\Column(type: 'integer', length: 1)]
    private int $state;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Field('datetime')]
    #[Visibility('all')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private ?DateTimeInterface $registrationDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Field('datetime')]
    #[Visibility('all')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private ?DateTimeInterface $lastLoginDate = null;

    public function __construct()
    {
        $this->state = State::STATE_UNACTIVATED;
    }

    public function getState(): State
    {
        return new State($this->state);
    }

    public function getRegistrationDate(): DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function getLastLoginDate(): ?DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setState(State $state): void
    {
        $this->state = $state->getValue();
    }

    public function setRegistrationDate(DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function setLastLogin(DateTimeInterface $lastLogin): void
    {
        $this->lastLoginDate = $lastLogin;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'person' => $this->person->toArray(),
            'state' => $this->state,
            'registrationDate' => $this->registrationDate,
            'lastLoginDate' => $this->lastLoginDate,
        ];
    }
}
