<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use rootio\Bundle\vmailmeBundle\Repository\WardenRepository;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class WardenCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('vmailme:warden')
                ->setDescription('Warden')
                ->addArgument('email', InputArgument::REQUIRED, 'Email?')
        ;
    }

    protected function getSpammer($email) {

        $em = $this->getContainer()->get('doctrine')->getManager();

        // 100 emails per day limit
        $last24h = $em->createQuery(
               'SELECT d.sender, COUNT(d.sender)
                FROM rootiovmailmeBundle:Delivery d
                WHERE d.sender = :email
                AND d.date > :last24h
                GROUP BY d.sender HAVING COUNT(d.sender) >= :quota_smtp'
                )
                ->setParameters(array(
                    'email'      => $email,
                    'last24h' => new \DateTime('-24 hours'),
                    'quota_smtp' => 100
                ))
                ->getResult();

        // 500 emails per month limit
        $thisMonth = $em->createQuery(
               'SELECT d.sender, COUNT(d.sender)
                FROM rootiovmailmeBundle:Delivery d
                WHERE d.sender = :email
                AND d.date > :thisMonth
                GROUP BY d.sender HAVING COUNT(d.sender) >= :quota_smtp'
                )
                ->setParameters(array(
                    'email'      => $email,
                    'thisMonth' => new \DateTime('-1 month'),
                    'quota_smtp' => 500
                ))
                ->getResult();

        if ($last24h || $thisMonth) {
            return true;
        } else {
            return false;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $email = $input->getArgument('email');

        if ($this->getSpammer($email)) {

            $command = $this->getApplication()->find('vmailme:ban');

            $arguments = array(
                'command' => $command->getName(),
                'emailOrUsername' => $email,
                'reason' => 'spam',
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }
    }
}
