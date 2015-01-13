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
class PostfixDeleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmailme:pfdel')
            ->setDescription('Delete messages from Postfix queue')
            ->addArgument('emailOrUsername', InputArgument::REQUIRED, 'Email or username?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailOrUsername = $input->getArgument('emailOrUsername');

        $user = $this->getContainer()->get('doctrine')
            ->getRepository('rootiovmailmeBundle:User')
            ->loadUserByUsername($emailOrUsername);

        if ($user) {
            $command = '/usr/local/bin/pfdel ' . $user->getEmail();
        } else {
            $command = '/usr/local/bin/pfdel ' . $emailOrUsername;
        }
        exec($command);
    }
}
