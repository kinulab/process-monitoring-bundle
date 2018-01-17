<?php

// Necessary to catch SIGINT and SIGTERM
declare(ticks = 1);

namespace Kinulab\ProcessMonitoringBundle\Monitor;

use Kinulab\ProcessMonitoringBundle\Service\ServiceDescriptorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service that monitor monitored.services
 */
class Monitor
{

    /**
     * list of monitored services
     *
     * @var iterable
     */
    protected $services;

    /**
     * Planification of checks
     *
     * @var array
     */
    protected $planification = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Is the monitoring running or not ?
     *
     * @var bool
     */
    protected $running;

    /**
     *
     * @param iterable $services
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($services, LoggerInterface $logger, EventDispatcherInterface $dispatcher)
    {
        $this->services = $services;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Start monitoring services
     */
    public function startMonitoring(){
        $this->dispatcher->dispatch('monitoring.services.starting');
        $this->logger->notice("Starting monitoring the services");

        if(!cli_set_process_title("kinulab_monitoring")){
            $this->logger->info("Unable to rename process to 'kinulab_monitoring'.");
        }

        pcntl_signal(SIGINT, [$this, 'stopServices']);
        pcntl_signal(SIGTERM, [$this, 'stopServices']);

        $this->running = true;
        $this->startPanification();

        $this->dispatcher->dispatch('monitoring.services.started');
        while($this->running){
            $this->check();

            $sleep = min($this->planification) - time();
            if($sleep > 0){
                sleep( $sleep );
            }else{
                sleep(10);
            }
        }
    }

    protected function stopServices($sig){
        $this->dispatcher->dispatch('monitoring.services.stopping');

        $this->running = false;
        switch ($sig){
            case SIGINT:
                $this->logger->notice("Interrupt signal catch. Stopping services...");
                break;
            case SIGTERM:
                $this->logger->notice("Termination signal catch. Stopping services...");
                break;
        }

        foreach($this->services as $serviceDescriptor){
            if($serviceDescriptor->isRunning()){
                $serviceDescriptor->stop();
            }
        }

        $this->dispatcher->dispatch('monitoring.services.stopped');
    }

    /**
     * Start necessary services and register planification
     */
    protected function startPanification(){
        if(0 === count($this->services)){
            $message = "There is no services registered, there is nothing to do.";
            $this->logger->error($message);
            throw new \Exception($message);
        }

        $services = [];
        foreach($this->services as $i => $service){
            if(!$service instanceof ServiceDescriptorInterface){
                $message = "The service must implements ".ServiceDescriptorInterface::class." : ".get_class($service)." given";
                $this->logger->error($message);
                throw new \Exception($message);
            }

            $services[$i] = $service;
        }
        // We overwrite the Generator to get a classic array
        $this->services = $services;

        foreach($this->services as $i => $service){
            $this->checkProcess($service);
            $this->planification[$i] = time()+$service->getCheckInterval();
        }
    }

    /**
     * Check service based on planification
     */
    protected function check(){
        foreach($this->planification as $i => $time){
            if($time >= time()){
                $this->checkService($this->services[$i]);
                $this->planification[$i] = time()+$this->services[$i]->getCheckInterval();
            }
        }
    }

    /**
     * Check if the service should be started/stopped
     */
    protected function checkService(ServiceDescriptorInterface $serviceDescriptor){
        if($serviceDescriptor->getExplicitStart() && $serviceDescriptor->allowedToBeRunning() && !$serviceDescriptor->isRunning()){
            $this->logger->info("Starting service : ".$serviceDescriptor->getName());
            $serviceDescriptor->start();
        }elseif($serviceDescriptor->getExplicitStop() && !$serviceDescriptor->allowedToBeRunning() && $serviceDescriptor->isRunning()){
            $this->logger->info("Stopping service : ".$serviceDescriptor->getName());
            $serviceDescriptor->stop();
        }
    }
}