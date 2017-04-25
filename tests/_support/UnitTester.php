<?php

use Codeception\Scenario;
use Del\Common\Config\DbCredentials;
use Del\Common\ContainerService;
use Del\UserPackage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

   public function __construct(Scenario $scenario)
   {
       parent::__construct($scenario);
       $creds = require_once 'migrant-cfg.php';
       $dbCredentials = new DbCredentials($creds['db']);
       $userPackage = new UserPackage();
       $containerSvc = ContainerService::getInstance();
       $containerSvc->setDbCredentials($dbCredentials);
       $containerSvc->registerToContainer($userPackage);
       $containerSvc->getContainer();
   }
}
