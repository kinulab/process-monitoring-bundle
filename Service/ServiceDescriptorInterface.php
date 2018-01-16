<?php

namespace Kinulab\ProcessMonitoringBundle\Service;

use Symfony\Component\Process\Process;

/**
 * Interface to describe a service to monitor
 */
interface ServiceDescriptorInterface
{

    /**
     * Get human name to describe the service
     */
    public function getName() :string;

    /**
     * Define if the service must be always started when allowed to run
     * @return bool
     */
    public function getExplicitStart() :bool;


    /**
     * Define if the service must be stopped explicitly if he's running and not allowed to
     * @return bool
     */
    public function getExplicitStop() :bool;


    /**
     * Get the interval between two checks (in seconds)
     * @return int
     */
    public function getCheckInterval() :int;

    /**
     * Valid if the service is allowed to run
     * @return bool
     */
    public function allowedToBeRunning() :bool;

    /**
     * Get the described service
     * @retourn Process
     */
    public function getProcess() :Process;

}