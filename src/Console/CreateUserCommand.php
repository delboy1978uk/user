<?php

declare(strict_types=1);

namespace Del\Console;

use DateTime;
use Del\Common\ContainerService;
use Del\Criteria\UserCriteria;
use Del\Service\UserService;
use Del\Value\User\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:create')
            ->setDescription('Create a user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create user');
        $email = $io->ask('What is the users email?');
        $criteria = new UserCriteria();
        $criteria->setEmail($email);
        $user = $this->userService->findOneByCriteria($criteria);

        if ($user !== null) {
            $output->writeln('User already exists.');

            return Command::FAILURE;
        }

        $name = $io->ask('What is the users name?');
        $password = $io->askHidden('Enter a password');
        $user = $this->userService->createFromArray([
            'email' => $email,
            'state' => State::STATE_ACTIVATED
        ]);
        $this->userService->changePassword($user, $password);
        $person = $user->getPerson();
        $person->setFirstname($name);
        $this->userService->getPersonService()->savePerson($person);
        $user->setPerson($person);
        $this->userService->saveUser($user);
        $io->success('User created.');

        return Command::SUCCESS;
    }
}
