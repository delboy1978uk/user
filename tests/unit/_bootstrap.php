<?php

use Del\Common\Config\DbCredentials;
use Del\Common\ContainerService;
use Del\UserPackage;

<<<<<<< HEAD
$creds = require_once '.migrant';

=======
$creds = require_once 'config/db.php';
>>>>>>> dev-master
$dbCredentials = new DbCredentials($creds['db']);
$userPackage = new UserPackage();
$containerSvc = ContainerService::getInstance();
$containerSvc->setDbCredentials($dbCredentials);
$containerSvc->registerToContainer($userPackage);
$containerSvc->getContainer();
