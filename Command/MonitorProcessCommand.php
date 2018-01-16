<?php

namespace Kinulab\ProcessMonitoringBundle\Command;

use Kinulab\ProcessMonitoringBundle\Monitor\Monitor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorProcessCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('monitor:services')
                ->setDescription('Start the monitoring of services')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $pid = pcntl_fork();
        if ($pid == -1) {
             die('could not fork');
        } else if ($pid) {
            $output->writeln("Monitoring started [<info>OK</info>]");
        } else {
            $monitor = $this->getContainer()->get(Monitor::class);
            $monitor->startMonitoring();
        }

    }

}
