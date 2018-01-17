<?php

namespace Kinulab\ProcessMonitoringBundle\Service;

use Symfony\Component\Process\Process;

/**
 * Abstract Description of a monitored service
 */
abstract class AbstractServiceDescriptor implements ServiceDescriptorInterface
{

    /**
     * Process name (for humans)
     *
     * @var string
     */
    protected $name;

    /**
     * The process to be running
     *
     * @var Process
     */
    protected $process;

    /**
     * Check interval in seconds
     *
     * @var int
     */
    protected $checkInterval = 60;

    /**
     * if the process should be automaticaly stared as long he's allowed to run
     *
     * @var bool
     */
    protected $explicitStart = false;

    /**
     * if the process should be automaticaly stopped when he's not allowed to run
     *
     * @var bool
     */
    protected $explicitStop = false;


    /**
     * Valid the constraints to define if the process is allowed to run
     *
     * @return bool
     */
    abstract public function allowedToBeRunning() :bool;

    /**
     * {@inheritdoc}
     */
    public function getName() :string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getExplicitStart() :bool
    {
        return $this->explicitStart;
    }

    /**
     * {@inheritdoc}
     */
    public function getExplicitStop() :bool
    {
        return $this->explicitStop;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckInterval() :int
    {
        return $this->checkInterval;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess() :Process
    {
        return $this->process;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning() :bool
    {
        return $this->process->isRunning();
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->process->start();
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->process->stop();
    }

}