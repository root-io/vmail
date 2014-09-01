<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use rootio\Bundle\vmailmeBundle\Entity\Delivery;

/**
 * @author David Routhieau <rootio@vmail.me>
 */
class DeliveryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmailme:delivery')
            ->setDescription('Delivery logger for emails')
            ->addArgument('sender', InputArgument::REQUIRED, 'Sender?')
            ->addArgument('ip', InputArgument::REQUIRED, 'IP?')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Recipient?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sender = $input->getArgument('sender');
        $ip = $input->getArgument('ip');
        $recipient = $input->getArgument('recipient');

        $em = $this->getContainer()->get('doctrine')->getManager();

        $delivery = new Delivery();
        $delivery->setSender($sender);
        $delivery->setIp($ip);
        $delivery->setRecipient($recipient);

        $em->persist($delivery);

        $em->flush();
    }
}
