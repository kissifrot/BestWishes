<?php

declare(strict_types=1);

namespace BestWishes\Command;

use BestWishes\Entity\ListEvent;
use BestWishes\Entity\User;
use BestWishes\Manager\UserManager;
use BestWishes\Repository\ListEventRepository;
use BestWishes\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'bw:setup', description: 'Setup BestWishes application')]
class SetupCommand extends Command
{
    private SymfonyStyle $style;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserManager            $userManager,
        private readonly ValidatorInterface       $validator,
        private readonly UserRepository         $userRepository,
        private readonly ListEventRepository         $listEventRepository,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->style = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->style->title('BestWishes Setup');
        try {
            $users = $this->userRepository->findAll();
            $this->style->success('Database connection established.');
        } catch (\Exception $e) {
            $this->style->error('Database connection failed: ' . $e->getMessage());
            $this->style->info('Please check your database connection settings in the .env file.');

            return Command::FAILURE;
        }
        if (empty($users)) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('No users found in the database. At least an admin user in required for Bestwishes to work. Do you want to create one?', false);

            if (!$helper->ask($input, $output, $question)) {
                $this->style->warning('User creation aborted.');
                return Command::FAILURE;
            }
            $result = $this->createAdminUser();
            if ($result !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        $activeListEvents = $this->listEventRepository->findAllActive();
        if (!empty($activeListEvents)) {
            $this->style->success('List events already exist in the database.');
            return Command::SUCCESS;
        }
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('No list events found in the database. Do you want to create default list events?', false);
        if (!$helper->ask($input, $output, $question)) {
            $this->style->warning('Events creation aborted.');
            return Command::FAILURE;
        }

        $this->createDefaultEvents();

        return Command::SUCCESS;
    }

    private function createAdminUser(): int
    {
        $this->style->title('Creating admin user');
        $username = $this->style->ask('Enter username', 'bw-admin-changeme', function (string $username): string {
            if (empty($username)) {
                throw new \RuntimeException('Username cannot be empty.');
            }

            return $username;
        });
        $email = $this->style->ask('Enter email', 'mail@example.com', function (string $email): string {
            if (empty($email)) {
                throw new \RuntimeException('Email cannot be empty.');
            }

            return $email;
        });
        $password = $this->style->askHidden('Enter password', function (string $password): string {
            if (empty($password)) {
                throw new \RuntimeException('Password cannot be empty.');
            }

            return $password;
        });

        $user = new User();
        $user->setUserIdentifier($username);
        $user->setEmail($email);
        $this->userManager->updatePassword($user, $password);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $errors = $this->validator->validate($user);

        if (\count($errors) > 0) {
            $errorsString = $errors->__toString();
            $this->style->error('Validation failed: ' . $errorsString);
            $this->style->error('Please check the input data and try again.');
            return Command::FAILURE;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->style->success(\sprintf('Admin user "%s" created successfully.', $username));

        return Command::SUCCESS;
    }

    private function createDefaultEvents(): void
    {
        $this->style->title('Creating default events');

        $christmas = new ListEvent(true, 'Christmas');
        $christmas->setMonth(12);
        $christmas->setDay(25);
        $this->entityManager->persist($christmas);
        $this->entityManager->flush();
        $newYear = new ListEvent(true, 'New Year');
        $newYear->setMonth(1);
        $newYear->setDay(1);
        $this->entityManager->persist($newYear);
        $this->entityManager->flush();

        $this->style->success('Default events (christmas and new year) created successfully.');
    }
}
