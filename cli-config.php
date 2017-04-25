<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Del\Common\ContainerService;
use Del\Common\Config\DbCredentials;

$config = require_once 'migrant-cfg.php';
$credentials = new DbCredentials($config['db']);

$container = ContainerService::getInstance()
    ->setDbCredentials($credentials)
    ->addEntityPath('src/Entity')
    ->getContainer();
/** @var Doctrine\ORM\EntityManager $em*/
$em = $container['doctrine.entity_manager'];
$helperSet = ConsoleRunner::createHelperSet($em);
$helperSet->set(new \Symfony\Component\Console\Helper\DialogHelper(),'dialog');
$cli = ConsoleRunner::createApplication($helperSet,[]);
return $cli->run();
