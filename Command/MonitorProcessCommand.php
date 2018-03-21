<?php

namespace Kinulab\ProcessMonitoringBundle\Command;

use Kinulab\ProcessMonitoringBundle\Monitor\Monitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorProcessCommand extends Command {

    protected $monitor;


    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;

        parent::__construct();
    }

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
            $this->monitor->startMonitoring();
        }

    }

}
