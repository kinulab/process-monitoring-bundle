<?php

namespace Kinulab\ProcessMonitoringBundle\Command;

use Kinulab\ProcessMonitoringBundle\Monitor\Monitor;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorProcessCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('monitor:process')
                ->setDescription('Start monitoring process')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $monitor = $this->getContainer()->get(Monitor::class);
        $monitor->startMonitoring();
    }

}
