<?php

declare(strict_types=1);

namespace Del\Service;

use DateTime;
use Del\Criteria\UserCriteria;
use Del\Entity\EmailLink;
use Del\Entity\User;
use Del\Person\Entity\Person;
use Del\Entity\UserInterface;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
use Del\Repository\EmailLink as EmailLinkRepository;
use Del\Repository\UserRepository;
use Del\Person\Service\PersonService;
use Del\Value\User\State;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Laminas\Crypt\Password\Bcrypt;

class UserService
{
    private string $userClass;
    private UserRepository $userRepository;

    public function __construct(
        protected EntityManager $entityManager,
        private  PersonService $personService
    ) {
        $this->setUserClass(User::class);
    }

    public function createFromArray(array $data): UserInterface
    {
        /** @var UserInterface $user */
        $user = new $this->userClass();
        $person = isset($data['person']) ? $data['person'] : new Person();
        $user->setPerson($person);
        isset($data['id']) ? $user->setId($data['id']) : null;
        isset($data['email']) ? $user->setEmail($data['email']) : null;
        isset($data['password']) ? $user->setPassword($data['password']) : null;
        isset($data['state']) ? $user->setState(new State($data['state'])) : null;
        isset($data['registrationDate']) ? $user->setRegistrationDate(new DateTime($data['registrationDate'])) : null;
        isset($data['lastLogin']) ? $user->setLastLogin(new DateTime($data['lastLogin'])) : null;

        return $user;
    }

    public function toArray(UserInterface $user): array
    {
        return [
            'id' => $user->getID(),
            'email' => $user->getEmail(),
            'person' => $user->getPerson(),
            'password' => $user->getPassword(),
            'state' => $user->getState()->getValue(),
            'registrationDate' => $user->getRegistrationDate() === null ? null : $user->getRegistrationDate()->format('Y-m-d H:i:s'),
            'lastLoginDate' => $user->getLastLoginDate() === null ? null : $user->getLastLoginDate()->format('Y-m-d H:i:s'),
        ];
    }

    public function saveUser(UserInterface $user): UserInterface
    {
        return $this->getUserRepository()->save($user);
    }

    public function findUserById(int $id): ?UserInterface
    {
        $criteria = new UserCriteria();
        $criteria->setId($id);
        $results = $this->getUserRepository()->findByCriteria($criteria);

        return $results && count($results) ? $results[0] : null;
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        $criteria = new UserCriteria();
        $criteria->setEmail($email);
        $result = $this->getUserRepository()->findByCriteria($criteria);

        return $result && count($result) ? $result[0] : null;
    }

    protected function getUserRepository(): UserRepository
    {
        if (!isset($this->userRepository)) {
            $this->userRepository = $this->entityManager->getRepository($this->userClass);
        }

        return $this->userRepository;
    }

    private function getEmailLinkRepository(): EmailLinkRepository
    {
        return $this->entityManager->getRepository(EmailLink::class);
    }

    public function hasProfile(UserInterface $user): bool
    {
        $has = false;
        $person = $user->getPerson();

        if (!empty($person->getFirstname()) && !empty($person->getLastname())) {
            $has = true;
        }

        return $has;
    }

    public function registerUser(array $data): UserInterface
    {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['confirm'])) {
            throw new InvalidArgumentException('Required fields missing', 400);
        }

        if ($data['password'] !== $data['confirm']) {
            throw new UserException(UserException::WRONG_PASSWORD);
        }

        $email = $data['email'];
        $password = $data['password'];
        $this->duplicateUserCheck($email);

        return $this->createUser($email, $password);
    }

    private function duplicateUserCheck(string $email): void
    {
        if($this->findUserByEmail($email)) {
            throw new UserException(UserException::USER_EXISTS, 400);
        }
    }

    /** this creates a new user from an email */
    public function registerNewUserWithoutPassword(string $email): UserInterface
    {
        $this->duplicateUserCheck($email);
        $password = $this->createRandomPassword();

        return  $this->createUser($email, $password);
    }

    private function createRandomPassword(): string
    {
        return \openssl_random_pseudo_bytes(12);
    }

    private function createUser(string $email, string $password): UserInterface
    {
        $person = new Person();
        /** @var UserInterface $user */
        $user = new $this->userClass();
        $state = new State(State::STATE_UNACTIVATED);
        $user->setPerson($person);
        $user->setEmail($email);
        $user->setRegistrationDate(new DateTime());
        $user->setState($state);
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);
        $encryptedPassword = $bcrypt->create($password);
        $user->setPassword($encryptedPassword);
        $this->saveUser($user);

        return $user;
    }

    public function changePassword(UserInterface $user, string $password): UserInterface
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);
        $encryptedPassword = $bcrypt->create($password);
        $user->setPassword($encryptedPassword);
        $this->saveUser($user);

        return $user;
    }

    public function generateEmailLink(UserInterface $user, int $expiry_days = 7): EmailLink
    {
        $date = new DateTime();
        $date->modify('+' . $expiry_days . ' days');
        $token = md5(uniqid((string) rand(), true));
        $link = new EmailLink();
        $link->setUser($user);
        $link->setToken($token);
        $link->setExpiryDate($date);

        return $this->getEmailLinkRepository()->save($link);
    }

    public function deleteEmailLink(EmailLink $link): void
    {
        /** @var EmailLink $link */
        $link = $this->entityManager->merge($link);
        $this->getEmailLinkRepository()->delete($link);
    }

    public function deleteUser(UserInterface $user, bool $deletePerson = false):  void
    {
        $this->getUserRepository()->delete($user,$deletePerson);
    }

    public function findEmailLink(string $email, string $token): EmailLink
    {
        $link = $this->getEmailLinkRepository()->findByToken($token);

        if(!$link) {
            throw new EmailLinkException(EmailLinkException::LINK_NOT_FOUND);
        }

        if($link->getUser()->getEmail() != $email) {
            throw new EmailLinkException(EmailLinkException::LINK_NO_MATCH);
        }

        if($link->getExpiryDate() < new DateTime()) {
            throw new EmailLinkException(EmailLinkException::LINK_EXPIRED);
        }

        return $link;
    }

    public function authenticate(string $email, string $password): int
    {
        $criteria = new UserCriteria();
        $criteria->setEmail($email);
        $user = $this->getUserRepository()->findByCriteria($criteria);

        if(empty($user)) {
            throw new UserException(UserException::USER_NOT_FOUND);
        }

        /** @var UserInterface $user  */
        $user = $user[0];

        switch($user->getState()->getValue()) {
            case State::STATE_UNACTIVATED:
                throw new UserException(UserException::USER_UNACTIVATED);
            case State::STATE_DISABLED:
            case State::STATE_SUSPENDED:
                throw new UserException(UserException::USER_DISABLED);
            case State::STATE_BANNED:
                throw new UserException(UserException::USER_BANNED);
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        if(!$bcrypt->verify($password, $user->getPassword())) {
            throw new UserException(UserException::WRONG_PASSWORD);
        }

        return $user->getID();
    }

    public function findByCriteria(UserCriteria $criteria): array
    {
        return $this->getUserRepository()->findByCriteria($criteria);
    }

    public function findOneByCriteria(UserCriteria $criteria): ?UserInterface
    {
        $results = $this->getUserRepository()->findByCriteria($criteria);

        return count($results) > 0 ? $results[0] : null;
    }

    public function checkPassword(UserInterface $user, string $password): bool
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        return $bcrypt->verify($password, $user->getPassword());
    }

    public function setUserClass(string $fullyQualifiedClassName): void
    {
        $this->userClass = $fullyQualifiedClassName;
    }

    public function getPersonService(): PersonService
    {
        return $this->personService;
    }

    /** @deprecated use getPersonService() instead  */
    public function getPersonSvc(): PersonService
    {
        return $this->personService;
    }
}
