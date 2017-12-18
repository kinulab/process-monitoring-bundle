<?php

namespace Kinulab\ProcessMonitoringBundle\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Validation;

/**
 * Description of a monitored process
 */
class ProcessDescriptor implements ProcessDescriptorInterface
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
     * Constraints that define in which conditions the process is allowed to be running
     *
     * @var array
     */
    protected $constraints;

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
     *
     * @param string $name
     * @param Process $process
     * @param array $constraints
     */
    public function __construct(string $name, Process $process, array $constraints = []) {
        $this->name = $name;
        $this->process = $process;
        $this->constraints = $constraints;
    }

    public function getName() :string
    {
        return $this->name;
    }

    /**
     * Define that the process must be started explicitly if he's not running but allowed to
     * @param bool $explicitStart
     * @return $this
     */
    public function setExplicitStart(bool $explicitStart){
        $this->explicitStart = $explicitStart;

        return $this;
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
     * Define that the process must be stopped explicitly if he's running and not allowed to
     * @param bool $explicitStop
     */
    public function setExplicitStop(bool $explicitStop){
        $this->explicitStop = $explicitStop;

        return $this;
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
     * @param int $interval
     * @return $this
     */
    public function setCheckInterval(int $interval){
        $this->checkInterval = $interval;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getCheckInterval() :int
    {
        return $this->checkInterval;
    }

    /**
     * Valid the constraints to define if the process is allowed to run
     * @return bool
     */
    public function allowedToBeRunning() :bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($this, $this->constraints);

        return 0 === count($violations);
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

}