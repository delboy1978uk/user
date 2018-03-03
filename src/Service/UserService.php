<?php

namespace Del\Service;

use DateTime;
use Del\Criteria\UserCriteria;
use Del\Entity\EmailLink;
use Del\Person\Entity\Person;
use Del\Entity\UserInterface;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
use Del\Repository\UserRepository;
use Del\Person\Service\PersonService;
use Del\Value\User\State;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Pimple\Container;
use Zend\Crypt\Password\Bcrypt;

class UserService
{
    /** @var EntityManager $em */
    protected $em;

    /** @var  PersonService */
    private $personSvc;

    /** @var string $userClass */
    private $userClass;

    public function __construct(Container $c)
    {
        $this->em = $c['doctrine.entity_manager'];
        $this->personSvc = $c['service.person'];
        $this->setUserClass('\Del\Entity\User');
    }

   /** 
    * @param array $data
    * @return UserInterface
    */
    public function createFromArray(array $data)
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




    /**
     * @return array
     */
    public function toArray(UserInterface $user)
    {
        return array
        (
            'id' => $user->getID(),
            'email' => $user->getEmail(),
            'person' => $user->getPerson(),
            'password' => $user->getPassword(),
            'state' => $user->getState()->getValue(),
            'registrationDate' => is_null($user->getRegistrationDate()) ? null : $user->getRegistrationDate()->format('Y-m-d H:i:s'),
            'lastLoginDate' => is_null($user->getLastLoginDate()) ? null : $user->getLastLoginDate()->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @param UserInterface $user
     * @return UserInterface
     */
    public function saveUser(UserInterface $user)
    {
        return $this->getUserRepository()->save($user);
    }

    /**
     * @param int $id
     * @return UserInterface|null
     */
    public function findUserById($id)
    {
        $criteria = new UserCriteria();
        $criteria->setId($id);
        $results = $this->getUserRepository()->findByCriteria($criteria);
        return (count($results)) ? $results[0] : null;
    }

    /**
     * @param string $email
     * @return UserInterface|null
     */
    public function findUserByEmail($email)
    {
        $criteria = new UserCriteria();
        $criteria->setEmail($email);
        $result = $this->getUserRepository()->findByCriteria($criteria);
        return count($result) ? $result[0] : null;
    }

   /**
    * @return UserRepository
    */
    private function getUserRepository()
    {
        return $this->em->getRepository($this->userClass);
    }

    /**
     * @return \Del\Repository\EmailLink
     */
    private function getEmailLinkRepository()
    {
        return $this->em->getRepository('Del\Entity\EmailLink');
    }

    /**
     * @param array $data
     * @return UserInterface
     * @throws UserException
     */
    public function registerUser(array $data)
    {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['confirm'])) {
            throw new InvalidArgumentException('Required fields missing', 400);
        }
        if ($data['password'] !== $data['confirm']) {
            throw new UserException(UserException::WRONG_PASSWORD);
        }

        $criteria = new UserCriteria();
        $criteria->setEmail($data['email']);
        $user = $this->getUserRepository()->findByCriteria($criteria);
        if(!empty($user)) {
            throw new UserException(UserException::USER_EXISTS);
        }

        $person = new Person();
        /** @var UserInterface $user */
        $user = new $this->userClass();
        $state = new State(State::STATE_UNACTIVATED);
        $user->setPerson($person)
             ->setEmail($data['email'])
             ->setRegistrationDate(new DateTime())
             ->setState($state);

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        $encryptedPassword = $bcrypt->create($data['password']);
        $user->setPassword($encryptedPassword);

        $this->saveUser($user);
        return $user;
    }

    /**
     * @param UserInterface $user
     * @param $password
     * @return UserInterface
     */
    public function changePassword(UserInterface $user, $password)
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        $encryptedPassword = $bcrypt->create($password);
        $user->setPassword($encryptedPassword);

        $this->saveUser($user);
        return $user;
    }

    /**
     * @param UserInterface $user
     * @param int $expiry_days
     * @return EmailLink
     */
    public function generateEmailLink(UserInterface $user, $expiry_days = 7)
    {
        $date = new DateTime();
        $date->modify('+'.$expiry_days.' days');
        $token = md5(uniqid(rand(), true));
        $link = new EmailLink();
        $link->setUser($user);
        $link->setToken($token);
        $link->setExpiryDate($date);
        return $this->getEmailLinkRepository()->save($link);
    }

    /**
     * @param EmailLink $link
     */
    public function deleteEmailLink(EmailLink $link)
    {
        /** @var EmailLink $link */
        $link = $this->em->merge($link);
        $this->getEmailLinkRepository()->delete($link);
    }

    /**
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user, $deletePerson = false)
    {
        $this->getUserRepository()->delete($user,$deletePerson);
    }

    /**
     * @param string $email
     * @param string $token
     * @return EmailLink
     * @throws EmailLinkException
     */
    public function findEmailLink($email, $token)
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

    /**
     * @param string $email
     * @param string $password
     * @return int
     * @throws UserException
     */
    public function authenticate($email, $password)
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

        if(!$bcrypt->verify($password, $user->getPassword()))
        {
            throw new UserException(UserException::WRONG_PASSWORD);
        }

        return $user->getID();
    }

    /**
     * @param UserCriteria $criteria
     * @return array
     */
    public function findByCriteria(UserCriteria $criteria)
    {
        return $this->getUserRepository()->findByCriteria($criteria);
    }

    /**
     * @param UserCriteria $criteria
     * @return UserInterface|null
     */
    public function findOneByCriteria(UserCriteria $criteria)
    {
        $results = $this->getUserRepository()->findByCriteria($criteria);
        return count($results) > 0 ? $results[0] : null;
    }

    /**
     * @param UserInterface $user
     * @param $password
     * @return bool
     */
    public function checkPassword(UserInterface $user, $password)
    {
        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        return $bcrypt->verify($password, $user->getPassword());
    }

    /**
     * @param string $fullyQualifiedClassName
     */
    public function setUserClass($fullyQualifiedClassName)
    {
        $this->userClass = $fullyQualifiedClassName;
    }
}