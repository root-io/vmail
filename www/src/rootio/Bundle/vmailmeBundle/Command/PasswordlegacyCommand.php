<?php

namespace rootio\Bundle\vmailmeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author David Routhieau <rootio@vmail.me>
 * This feature needs to be remove at March, 2015
 */
class PasswordlegacyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmailme:passwordlegacy')
            ->setDescription('Fix old user password scheme')
            ->addArgument('emailOrUsername', InputArgument::REQUIRED, 'Email or username?')
            ->addArgument('password', InputArgument::REQUIRED, 'Password?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emailOrUsername = $input->getArgument('emailOrUsername');
        $password = $input->getArgument('password');

        $user = $this->getContainer()->get('doctrine')
            ->getRepository('rootiovmailmeBundle:User')
            ->loadUserByUsername($emailOrUsername);

        if ($user) {

            $salt = substr($user->getPasswordLegacy(), -8);
            $salt_ascii = $user->hexToAscii($salt);

            $ssha512hexHash = "{SSHA512.hex}" . hash('sha512', $password . $salt_ascii) . $salt;
            $sha256Hash = "{SHA256}" . hash('sha256', $password);

            $em = $this->getContainer()->get('doctrine')->getManager();

            if ($user->getPasswordLegacy() === $sha256Hash) {
                $user->setPasswordLegacy($password);

                $em->persist($user);
                $em->flush();

                $output->writeln('SHA256 password updated to SSHA512HEX');
            }

            if ($user->getPassword() === 'vmailme-temp-password' && $user->getPasswordLegacy() === $ssha512hexHash) {

                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($password, $user->getSalt());
                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                $output->writeln('Broken password updated');
            }
        }
    }
}
