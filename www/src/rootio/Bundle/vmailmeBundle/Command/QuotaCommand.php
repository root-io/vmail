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
class QuotaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmail:quota')
            ->setDescription('Quota warning email')
            ->addArgument('recipient')
            ->addArgument('percent')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipient = $input->getArgument('recipient');
        $percent = $input->getArgument('percent');

        $templateContent = $this->getContainer()->get('twig')->loadTemplate('rootiovmailmeBundle::Emailing/quota_warning.text.twig');

        $subject = $templateContent->renderBlock('subject', array());
        $body = $templateContent->renderBlock('body', array('percent' => $percent));

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
