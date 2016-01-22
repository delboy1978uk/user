<?php

namespace Del\Service;

use DateTime;
use Del\Criteria\UserCriteria;
use Del\Entity\EmailLink;
use Del\Entity\Person;
use Del\Entity\User as UserEntity;
use Del\Exception\EmailLinkException;
use Del\Exception\UserException;
use Del\Repository\User as UserRepository;
use Del\Service\Person as PersonService;
use Del\Value\User\State;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Zend\Crypt\Password\Bcrypt;

class User
{
    /** @var EntityManager $em */
    protected $em;

    /** @var  PersonService */
    private $personSvc;

    public function __construct(EntityManager $em, PersonService $personSvc)
    {
        $this->em = $em;
        $this->personSvc = $personSvc;
    }

   /** 
    * @param array $data
    * @return UserEntity
    */
    public function createFromArray(array $data)
    {
        $user = new UserEntity();
        $user->setPerson(new Person());
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
    public function toArray(UserEntity $user)
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
     * @param UserEntity $user
     * @return UserEntity
     */
    public function saveUser(UserEntity $user)
    {
        return $this->getUserRepository()->save($user);
    }

   /**
    * @return UserRepository
    */
    private function getUserRepository()
    {
        return $this->em->getRepository('Del\Entity\User');
    }

    /**
     * @return \Del\Repository\EmailLink
     */
    private function getEmailLinkRepository()
    {
        return $this->em->getRepository('Del\Entity\EmailLink');
    }

    public function registerUser(array $data)
    {
        if (!$data['email'] || !$data['password'] || !$data['confirm']) {
            throw new InvalidArgumentException();
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
        $user = new UserEntity();
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
     * @param UserEntity $user
     * @param int $expiry_days
     */
    public function generateEmailLink(UserEntity $user, $expiry_days = 7)
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
        $this->getEmailLinkRepository()->delete($link);
    }

    /**
     * @param $email
     * @param $token
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
    function authenticate($email, $password)
    {
        $criteria = new UserCriteria();
        $criteria->setEmail($email);

        /** @var UserEntity $user  */
        $user = $this->getUserRepository()->findByCriteria($criteria)[0];

        if(!$user) {
            throw new UserException(UserException::USER_NOT_FOUND);
        }

        switch($user->getState()->getValue()) {
            case State::STATE_UNACTIVATED :
                throw new UserException(UserException::USER_UNACTIVATED);
                break;
            case State::STATE_DISABLED :
            case State::STATE_SUSPENDED :
                throw new UserException(UserException::USER_DISABLED);
                break;
            case State::STATE_BANNED :
                throw new UserException(UserException::USER_BANNED);
                break;

        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);

        if(!$bcrypt->verify($password, $user->getPassword()))
        {
            throw new UserException(UserException::WRONG_PASSWORD);
        }

        return $user->getID();
    }
}
