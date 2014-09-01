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
class EnableCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmailme:enable')
            ->setDescription('Enable a user')
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

            $em = $this->getContainer()->get('doctrine')->getManager();

            $user->setIsEnabled(true);

            $em->persist($user);
            $em->flush();
        }
    }
}
