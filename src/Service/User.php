<?php

namespace Del\Service;

use DateTime;
use Del\Criteria\UserCriteria;
use Del\Entity\Person;
use Del\Entity\User as UserEntity;
use Del\Exception\UserException;
use Del\Repository\User as UserRepository;
use Del\Service\Person as PersonService;
use Del\Value\User\State;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;

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
        return $this->getRepository()->save($user);
    }

   /**
    * @return UserRepository
    */
    protected function getRepository()
    {
        return $this->em->getRepository('Del\Entity\User');
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
        $user = $this->getRepository()->findByCriteria($criteria);
        if(!is_null($user)) {
            throw new UserException(UserException::USER_EXISTS);
        }

        $person = new Person();
        $user = new UserEntity();
        $state = new State(State::STATE_UNACTIVATED);
        $user->setPerson($person)
             ->setEmail($data['email'])
             ->setRegistrationDate(new DateTime())
             ->setState($state);


    }
}
