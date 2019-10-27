<?php

use Del\Common\Config\DbCredentials;
use Del\Common\ContainerService;
use Del\UserPackage;

$creds = require_once '.migrant';
$dbCredentials = new DbCredentials($creds['db']);
$userPackage = new UserPackage();
$containerSvc = ContainerService::getInstance();
$containerSvc->setDbCredentials($dbCredentials);
$containerSvc->registerToContainer($userPackage);
$containerSvc->getContainer();
