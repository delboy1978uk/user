# User
[![Build Status](https://travis-ci.org/delboy1978uk/user.png?branch=master)](https://travis-ci.org/delboy1978uk/user) [![Code Coverage](https://scrutinizer-ci.com/g/delboy1978uk/user/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/user/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/delboy1978uk/user/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/delboy1978uk/user/?branch=master) <br />
A persistable User object and service.
## Installation
Install via composer into your project:
```
composer require delboy1978uk/user
```
## Database Setup
To set up the database tables (we use migrations to control this, so it's super quick), create a migrant-cfg.php with 
your db credentials:
  ```php
  <?php
  
  return [
      'db' => [
          'driver' => 'pdo_mysql',
          'dbname' => 'yourdb',
          'user' => 'yourusername',
          'password' => 'yourpass',
      ],
      'packages' => [
          'delboy1978uk/user',
      ],
  ];
  ```
  Then from the root of your site, call the following command:
```
migrant migrate
```
A message appears, type `y` to continue. Your tables have been created! If the command is not found, this means `vendor/bin` isn't in your path, you should add it! Edit your `~/.bashrc` or `~/.zshrc` with this:
```
export PATH=$PATH:vendor/bin
```
## Container Setup
This package uses `delboy1978uk/common`, which utilises Doctrine 2 as the ORM and Pimple as the dependency injection container.
 To use this package, we set up the container, and register the UserPackage:
```php
<?php

use Del\Common\Config\DbCredentials;
use Del\Common\ContainerService;
use Del\UserPackage;

$userPackage = new UserPackage();

$credentials = new DbCredentials([
    'driver' => 'pdo_mysql',
    'dbname' => 'yourdb',
    'user' => 'yourusername',
    'password' => 'yourpass',
]);


$containerSvc = ContainerService::getInstance();
$containerSvc->setDbCredentials($credentials);
$containerSvc->registerToContainer($userPackage);
```
Once you have registered the DB credentials and user package, you can call the user service (or anything else in your 
container) anywhere in your project in the following way:
```php
<?php 

$container = ContainerService::getInstance()->getContainer();
/** @var \Del\Service\User $userService */
$userService = $container['service.user'];
```
## The User Service
All manipulation of our User objects happens through the UserService, which has a variety of methods available:
```php
<?php

$user = $svc->createFromArray($data); // Pass an array, get a User object
$array = $svc->toArray($user); // Pass an User object, get an array
$user = $svc->saveUser($user); // Inserts or updates a User in the DB
$user = $svc->findUserById($id); // Finds a User in the DB
$user = $svc->findUserByEmail($email); // Finds a User in the DB
$user = $svc->changePassword($user, $password); // Updates a password in the DB
$users = $svc->findByCriteria($criteria); // See below for more info
$user = $svc->findOneByCriteria($criteria); // See below for more info
$svc->deleteUser($user); // Deletes a User from the DB
$svc->setUserClass($className); // If you wish to extend this class with your own
$svc->checkPassword($user, $plainPassword); // Returns true or false
$svc->registerUser($data); // See below
$svc->authenticate($email, $password); // Returns the user's ID on success
$emailLink = $svc->generateEmailLink($user, $daysTillExpiry); // For emailing with a secure token
$emailLink = $svc->findEmailLink($email, $token); // Finds the email link for that user
$emailLink = $svc->deleteEmailLink($link); // Deletes from the DB
```
### Registering a user
Pass in an array with keys `email`, `password`, and `confirm`, confirm being the password confirmation field.
```php
<?php
$user = $svc->registerUser($data);
```
The user will be in an unactivated state. Usually we would email the user with an activation link. To get a secure token, 
do the following:
```php
<?php
$emailLink = $svc->generateEmailLink($user, 7); // Token expires in 7 days
```
You can now email your user, and use findEmailLink when they arrive to activate their account:
```php
<?php
$emailLink = $svc->findEmailLink($email, $token); 
```
You can then update the users state to active and save.
## The User Entity
Usage as follows.
```php
<?php

use Del\Entity\User;
use Del\Person\Entity\Person;
use Del\Value\User\State;

$user = new User();
$user->setID(12345); // You shouldn't have to, the ORM will do this
$user->setEmail('a@b.com');
$user->setPassword($password); // Not encrypted - use the service which will in turn call this 
$user->setState(new State(State::STATE_ACTIVATED)); 
$user->setRegistrationDate($registrationDate); // A DateTime object
$user->setLastLogin($registrationDate); // A DateTime object
$user->setPerson(new Person()); // See delboy1978uk/person
```
The User lib also uses `delboy1978uk/person`, which you can use to store some personal details of the user, if you like. 
## The User Collection
This is just a fancy array, extending `Doctrine\Common\Collections\ArrayCollection`. It has the usual stuff:
```php
<?php

while ($collection->valid()) {
    $user = $collection->current();
    // Do things to user
    $collection->next();
}
```
## The User Repository
The Repository class is within the service, and contains all the database queries.
### Query Criteria
You can use a UserCriteria to refine the results returned like so:
```php
<?php

use Del\Criteria\UserCriteria;
use Del\Value\User\State;

$criteria = new UserCriteria();
$criteria->setState(State::STATE_UNACTIVATED); // We only want unactivated users
$users = $svc->findByCriteria($criteria);
```
You can also use `findOneByCriteria` to restrict results to one row.
