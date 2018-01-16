<?php

namespace Kinulab\ProcessMonitoringBundle\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Validation;

/**
 * Description of a monitored service
 */
class ServiceDescriptor extends AbstractServiceDescriptor
{

    /**
     * Constraints that define in which conditions the service is allowed to be running
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
     * Define that the service must be started explicitly if he's not running but allowed to
     * @param bool $explicitStart
     * @return $this
     */
    public function setExplicitStart(bool $explicitStart){
        $this->explicitStart = $explicitStart;

        return $this;
    }

    /**
     * Define that the service must be stopped explicitly if he's running and not allowed to
     * @param bool $explicitStop
     */
    public function setExplicitStop(bool $explicitStop){
        $this->explicitStop = $explicitStop;

        return $this;
    }

    /**
     * Define at witch frequency the monitor must check the state of the process
     *
     * @param int $interval in seconds
     * @return $this
     */
    public function setCheckInterval(int $interval){
        $this->checkInterval = $interval;

        return $this;
    }

    /**
     * Valid the constraints to define if the service is allowed to run
     * @return bool
     */
    public function allowedToBeRunning() :bool
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($this, $this->constraints);

        return 0 === count($violations);
    }

}