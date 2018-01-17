<?php

namespace Kinulab\ProcessMonitoringBundle\Process\Task;

use Cron\CronExpression;

/**
 * @author marc
 */
abstract class AbstractTaskDescriptor {

    /**
     * Cron schedule of the task
     * 
     * @var \Cron\CronExpression
     */
    protected $cron;

    /**
     * List of specific times the service must be run at
     *
     * @var array
     */
    protected $runAtDates;

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
     * Constraints that have to be validated to allow the process to run
     *
     * @var array
     */
    protected $constraints;

    /**
     * Retry count in case the task is due but cannot be executed because of
     * constraint violations
     *
     * @var int
     */
    protected $retryCount;

    /**
     * Retry interval in seconds
     *
     * @var int
     */
    protected $retryInterval;

    public function getName(): string {
        return $this->name;
    }

    public function getProcess(): Process {
        return $this->process;
    }

    function getCron(): CronExpression {
        return $this->cron;
    }

    public function getRunAtDates(): array {
        return $this->runAtDates;
    }

    public function getConstraints(): array {
        return $this->constraints;
    }

    function getRetryCount(): int {
        return $this->retryCount;
    }

    /**
     * Get the etry interval in seconds
     * @return int
     * 
     */
    public function getRetryInterval(): int {
        return $this->retryInterval;
    }

    /**
     * Get information about the task execution
     *
     * @return bool
     */
    abstract public function getStatus();

    /**
     * Get the next exectution time of the process
     *
     * @return bool
     */
    abstract public function getNextExecutionTime(): int;

    /**
     * Valid the constraints to define if the process is allowed to run
     *
     * @return bool
     */
    abstract public function allowedToBeRunning(): bool;
}
