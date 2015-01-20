<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use rootio\Bundle\vmailmeBundle\Entity\Ban;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class BanCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmail:ban')
            ->setDescription('Ban a user')
            ->addArgument('emailOrUsername', InputArgument::REQUIRED, 'Email or username?')
            ->addArgument('reason', InputArgument::REQUIRED, 'Reason?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailOrUsername = $input->getArgument('emailOrUsername');
        $reason = $input->getArgument('reason');

        $user = $this->getContainer()->get('doctrine')
            ->getRepository('rootiovmailmeBundle:User')
            ->loadUserByUsername($emailOrUsername);

        if ($user && $user->isEnabled()) {

            $em = $this->getContainer()->get('doctrine')->getManager();

            $user->setIsEnabled(false);

            $em->persist($user);

            $ban = new Ban();
            $ban->setUser($user);
            $ban->setType(Ban::TYPE_PERMANENTLY);
            $ban->setReason($reason);

            $em->persist($ban);
            $em->flush();

            $command = $this->getApplication()->find('vmail:pfdel');
            $arguments = array(
                'command'         => $command->getName(),
                'emailOrUsername' => $user->getEmail()
            );
            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }
    }
}
