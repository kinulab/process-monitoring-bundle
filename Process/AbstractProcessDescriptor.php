<?php

namespace Kinulab\ProcessMonitoringBundle\Process;

use Symfony\Component\Process\Process;

/**
 * Abstract Description of a monitored process
 */
abstract class AbstractProcessDescriptor implements ProcessDescriptorInterface
{
    /**
     * Process name (for humans)
     *
     * @var string
     */
    protected $name;

    /**
     * @var Process
     */
    protected $process;

    /**
     * Check interval in seconds
     * @var int
     */
    protected $checkInterval = 60;

    /**
     * if the process should be automaticaly stared as long he's allowed to run
     * @var bool
     */
    protected $explicitStart = false;

    /**
     * if the process should be automaticaly stopped when he's not allowed to run
     * @var bool
     */
    protected $explicitStop = false;


    /**
     * Valid the constraints to define if the process is allowed to run
     * @return bool
     */
    abstract public function allowedToBeRunning() :bool;


    public function getName() :string
    {
        return $this->name;
    }

    /**
     *
     * @return bool
     */
    public function getExplicitStart() :bool
    {
        return $this->explicitStart;
    }

    /**
     *
     * @return boo
     */
    public function getExplicitStop() :bool
    {
        return $this->explicitStop;
    }

    /**
     *
     * @return int
     */
    public function getCheckInterval() :int
    {
        return $this->checkInterval;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

}