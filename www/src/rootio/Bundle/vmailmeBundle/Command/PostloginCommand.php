<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use rootio\Bundle\vmailmeBundle\Entity\Login;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class PostloginCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmailme:postlogin')
            ->setDescription('Postlogin logger for IMAP/POP auth')
            ->addArgument('emailOrUsername', InputArgument::REQUIRED, 'Email or username?')
            ->addArgument('ip', InputArgument::REQUIRED, 'IP?')
            ->addArgument('protocol', InputArgument::REQUIRED, 'Protocol?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailOrUsername = $input->getArgument('emailOrUsername');
        $ip = $input->getArgument('ip');
        $protocol = $input->getArgument('protocol');

        $user = $this->getContainer()->get('doctrine')
            ->getRepository('rootiovmailmeBundle:User')
            ->loadUserByUsername($emailOrUsername);

        if ($user) {

            $em = $this->getContainer()->get('doctrine')->getManager();

            $login = new Login();
            $login->setEmail($emailOrUsername);
            $login->setIp($ip);
            $login->setProtocol($protocol);

            $em->persist($login);

            $em->flush();
        }
    }
}
