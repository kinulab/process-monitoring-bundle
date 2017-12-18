<?php

namespace Kinulab\ProcessMonitoringBundle\Monitor;

use Kinulab\ProcessMonitoringBundle\Process\ProcessDescriptorInterface;
use Psr\Log\LoggerInterface;

/**
 * Monitoring service that check described process
 */
class Monitor
{

    /**
     * list of described process
     * @var iterable
     */
    protected $processes;

    /**
     * Planification of checks
     * @var array
     */
    protected $planification = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     *
     * @param iterable $processes
     */
    public function __construct($processes, LoggerInterface $logger)
    {
        $this->processes = $processes;
        $this->logger = $logger;
    }

    /**
     * Start monitoring process
     */
    public function startMonitoring(){
        $this->logger->notice("Starting of process monitoring");

        if(!cli_set_process_title("kinulab_monitoring")){
            $this->logger->info("Unable to rename process to 'kinulab_monitoring'.");
        }

        $this->startPanification();

        while(1){
            $sleep = min($this->planification) - time();
            if($sleep > 0){
                sleep( $sleep );
            }else{
                sleep(10);
            }

            $this->check();
        }
    }

    /**
     * Start necessary process and register planification
     */
    protected function startPanification(){
        if(0 === count($this->processes)){
            $message = "There is no process registered, there is nothing to do.";
            $this->logger->error($message);
            throw new \Exception($message);
        }

        $processes = [];
        foreach($this->processes as $i => $process){
            if(!$process instanceof ProcessDescriptorInterface){
                $message = "The process must implements ".ProcessDescriptorInterface::class." : ".get_class($process)." given";
                $this->logger->error($message);
                throw new \Exception($message);
            }

            $processes[$i] = $process;
        }
        // We overwrite the Generator to get a classic array
        $this->processes = $processes;

        foreach($this->processes as $i => $process){
            $this->checkProcess($process);
            $this->planification[$i] = time()+$process->getCheckInterval();
        }
    }

    /**
     * Check process based on planification
     */
    protected function check(){
        foreach($this->planification as $i => $time){
            if($time >= time()){
                $this->checkProcess($this->processes[$i]);
                $this->planification[$i] = time()+$this->processes[$i]->getCheckInterval();
            }
        }
    }

    /**
     * Check if the process should be started/stopped
     */
    protected function checkProcess(ProcessDescriptorInterface $processDescriptor){
        $process = $processDescriptor->getProcess();
        if($processDescriptor->getExplicitStart() && $processDescriptor->allowedToBeRunning() && !$process->isRunning()){
            $this->logger->info("Starting process : ".$processDescriptor->getName());
            $process->start();
        }elseif($processDescriptor->getExplicitStop() && !$processDescriptor->allowedToBeRunning() && $process->isRunning()){
            $this->logger->info("Stopping process : ".$processDescriptor->getName());
            $process->stop();
        }
    }
}