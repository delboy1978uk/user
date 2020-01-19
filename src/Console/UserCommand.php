<?php

namespace Del\Console;

use DateTime;
use Del\Common\ContainerService;
use Del\Criteria\UserCriteria;
use Del\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCommand extends Command
{
    /** @var UserService $userService */
    private $userService;

    /**
     * UserCommand constructor.
     * @throws \Doctrine\ORM\ORMException
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('reset-pass')
            ->setDescription('Resets a user\'s password')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'The email of the user'
            )->addArgument(
                'newPassword',
                InputArgument::REQUIRED,
                'The email of the user'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $pass = $input->getArgument('newPassword');
        $criteria = new UserCriteria();
        $criteria->setEmail($email);
        $user = $this->userService->findOneByCriteria($criteria);
        if ($user === null) {
            $output->writeln('No User Found.');
        } else {
            $this->userService->changePassword($user, $pass);
            $this->userService->saveUser($user);
            $output->writeln('Password changed for '.$email);
        }
    }
}
