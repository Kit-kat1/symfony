<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;


class SendAlertCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setName('run:send-alerts')
            ->setDescription('Sending alerts for users which should get notifications.')
            ->addOption(
                'token',
                null,
                InputOption::VALUE_REQUIRED,
                'Without token permission will denied'
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('token') == $this->getContainer()->getParameter('send.alert.access.token')) {
            while (true) {
                $checks = $this->getContainer()->get('app.pingdom_connect')->connect();
                $down = $this->getContainer()->get('app.pingdom_websites_status')->sitesDown($checks);
                $up = $this->getContainer()->get('app.pingdom_websites_status')->sitesUp($checks);
                $this->getContainer()->get('app.pingdom_send_alert')->sendMail($down, $up);
                $output->writeln(sprintf('Send alert'));
                sleep(60);
            }
        } else {
            $output->writeln(sprintf('Invalid token'));
        }
    }
}
