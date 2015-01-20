<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class RegisterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmail:register')
            ->setDescription('Register a user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username?')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Recipient?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = sha1(uniqid('', true));
        $recipient = $input->getArgument('recipient');

        $user = $this->getContainer()->get('rootiovmailme.user_manager')->createUser($username, $password);

        if ($user) {

            $templateContent = $this->getContainer()->get('twig')->loadTemplate('rootiovmailmeBundle::Emailing/register_command.text.twig');

            $subject = $templateContent->renderBlock('subject', array());
            $body = $templateContent->renderBlock('body', array('username' => $username, 'password' => $password));

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom('noreply@vmail.me')
                ->setTo($recipient)
                ->setBody($body)
            ;
            $container = $this->getContainer();
            $mailer = $container->get('mailer');
            $mailer->send($message);

            $spool = $mailer->getTransport()->getSpool();
            $transport = $container->get('swiftmailer.transport.real');

            $spool->flushQueue($transport);
        }
    }
}
