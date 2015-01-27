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
class CleanLogsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('vmail:cleanlogs')
            ->setDescription('Clean server logs')
        ;
    }

    private function removeMaildir()
    {
        // FIXME
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // FIXME
        // Disable inactive users
        // Users with 12 months of inactivity (check vmailme.User.lastActivity):
        //   - Remove from vmailme.Ban
        //   - Remove from vmailme.ForgotPassword
        //   - Remove from vmailme.User
        //   - Remove Roundcube data
        //   - Remove Maildir !DANGER!
    }
}
