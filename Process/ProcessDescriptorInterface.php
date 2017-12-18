<?php

namespace Kinulab\ProcessMonitoringBundle\Process;

use Symfony\Component\Process\Process;

/**
 * Interface for described process to monitor
 */
interface ProcessDescriptorInterface
{

    /**
     * Get human name to describe the process
     */
    public function getName() :string;

    /**
     * Define if the process must be always started when allowed to run
     * @return bool
     */
    public function getExplicitStart() :bool;


    /**
     * Define if the process must be stopped explicitly if he's running and not allowed to
     * @return bool
     */
    public function getExplicitStop() :bool;


    /**
     * Get the interval between two checks (in seconds)
     * @return int
     */
    public function getCheckInterval() :int;

    /**
     * Valid if the process is allowed to run
     * @return bool
     */
    public function allowedToBeRunning() :bool;

    /**
     * Get the described process
     * @retourn Process
     */
    public function getProcess() :Process;

}