<?php

namespace Kinulab\ProcessMonitoringBundle\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Validation;

/**
 * Description of a monitored process
 */
class ProcessDescriptor extends AbstractProcessDescriptor
{

    /**
     * Constraints that define in which conditions the process is allowed to be running
     *
     * @var array
     */
    protected $constraints;

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
     * Define that the process must be stopped explicitly if he's running and not allowed to
     * @param bool $explicitStop
     */
    public function setExplicitStop(bool $explicitStop){
        $this->explicitStop = $explicitStop;

        return $this;
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
     * Valid the constraints to define if the process is allowed to run
     * @return bool
     */
    public function allowedToBeRunning() :bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($this, $this->constraints);

        return 0 === count($violations);
    }

}