<?php

namespace Kinulab\ProcessMonitoringBundle\Process\Task;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Process\Process;
use Cron\CronExpression;

/**
 * Description of TaskDescriptor
 *
 * @author marc
 */
class TaskDescriptor extends AbstractTaskDescriptor {

    protected $lastExecutionDate;

    /**
     *
     * @param string $cronExpression
     * @param string $name
     * @param Process $process
     * @param array $runAtDates
     * @param array $constraints
     */
    public function __construct(string $name, Process $process, array $constraints = []) {
        $this->name = $name;
        $this->process = $process;
        $this->constraints = $constraints;
    }

    public function setCronExpression($expression) {
        if (!CronExpression::isValidExpression($expression)) {
            throw new \InvalidArgumentException('Invalid Cron expression :"' . $expression . "'");
        }
        $this->cron->setExpression($expression);
    }

    public function addRunAt(\DateTime $runAt) {
        arrayPush($this->runAtDates, $runAt);
    }

    /**
     * Get the next runAt date
     * @return DateTime
     */
    private function getNextRunAtDate(): DateTime {
        $dates = $this->getRunAtDates();
        if (empty($dates)) {
            return null;
        }
        return min($dates);
    }

    /**
     * Return the runAt dates that are after the last process run date
     * @return array
     */
    public function getRunAtDates(): array {
        $this->removeStaleRunAtDates();
        return $this->runAtDates;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextExecutionTime() {

        $dates = [];

        $nextCronDate = isset($this->cron) ? $this->cron->getNextRunDate() : null;
        array_push($dates, $nextCronDate);

        $nextRunAtDate = $this->getNextRunAtDate();
        array_push($dates, $nextRunAtDate);

        $validDates = array_filter($dates, 'isset');

        return  empty($validDates) ? null : min($validDates);
    }

    /**
     * {@inheritdoc}
     */
    public function allowedToBeRunning() {

        return $this->cron->isDue() &&
                $this->isConstraintsValidated() &&
                $this->runAtTimeIsDue();
    }

    private function runAtTimeIsDue(): bool {

        $nextRunAtDates = $this->getValidRunAtDates();
        $minRunatDate = min($nextRunAtDates);

        return $minRunatDate->getTimestamp() - time() <= 0;
    }

    private function removeStaleRunAtDates($dates, $thresholdDate) {

        return array_filter($this->runAtDates, function($date) {
            return $date <= $this->lastExecutionDate;
        });
    }

    private function isConstraintsValidated() {

        $validator = Validation::createValidator();
        $violations = $validator->validate($this, $this->constraints);

        return 0 === count($violations);
    }

    public function start() {
        $this->process->start();
        $this->lastExecutionDate = new \DateTime();
    }

    public function stop() {
        $this->process->stop();
    }

    public function getStatus() {
        
    }

}
