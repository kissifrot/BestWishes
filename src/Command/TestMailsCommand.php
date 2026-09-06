<?php

declare(strict_types=1);

namespace BestWishes\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Exception\RfcComplianceException;

#[AsCommand(
    name: 'bw:test-mails',
    description: 'Send a test email directly to the supplied address to check if the mailer is working correctly.',
)]
class TestMailsCommand extends Command
{
    private SymfonyStyle $style;
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromAddress,
        private readonly string $siteName,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->style = new SymfonyStyle($input, $output);
    }

    protected function configure(): void
    {
        $this->addArgument(
            'recipient',
            InputArgument::REQUIRED,
            'Email address that receives the test email'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $recipient */
        $recipient = $input->getArgument('recipient');

        try {
            $email = (new Email())
                ->from($this->fromAddress)
                ->to($recipient)
                ->subject(\sprintf('[%s] Test email', $this->siteName))
                ->text('This is a test email sent directly by the bw:test-mails command.');

            $this->mailer->send($email);
        } catch (RfcComplianceException $exception) {
            $this->style->error(\sprintf('Invalid email address: "%s"', $exception->getMessage()));

            return Command::FAILURE;
        } catch (TransportExceptionInterface $exception) {
            $this->style->error(\sprintf('Unable to send the test email: %s', $exception->getMessage()));

            return Command::FAILURE;
        }

        $this->style->success(\sprintf('Test email sent directly to %s.', $recipient));

        return Command::SUCCESS;
    }
}
