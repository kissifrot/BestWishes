<?php


namespace BestWishes\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateAdminUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('bw:admin-user:create')
            ->setDescription('Create an admin user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('name', InputArgument::REQUIRED, 'The display name'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $manipulator = $this->getContainer()->get('bw.util.user_manipulator');
        $manipulator->create($username, $name, $password, $email, true, true);

        $output->writeln(sprintf('<info>Successfully created admin user </info><comment>%s</comment>', $username));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = array();

        if (!$input->getArgument('username')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \RuntimeException('Username cannot be empty');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if (!$input->getArgument('name')) {
            $question = new Question('Please choose a display name:');
            $question->setValidator(function ($name) {
                if (empty($name)) {
                    throw new \RuntimeException('Display name cannot be empty');
                }

                return $name;
            });
            $questions['name'] = $question;
        }

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \RuntimeException('Email cannot be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \RuntimeException('Password cannot be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
