<?php

namespace Kinulab\ProcessMonitoringBundle\Process\Task;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Process\Process;

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
    public function __construct(string $cronExpression, string $name, Process $process, \DateTime $runAtDates = [], rray $constraints = []) {
        $this->name = $name;
        $this->process = $process;
        $this->constraints = $constraints;
        $this->cron->setExpression($expression);
        $this->runAtDates = $runAtDates;
    }
    
    public function setCronExpression($expression) {
        assert('CronExpression::isValidExpression($expression)');
        $this->cron->setExpression($expression);
    }
    
    /**
     * Return the runAt dates that are after the last process run date
     * @return array
     */
    public function getValidRunAtDates() :array {
         $this->removeStaleRunAtDates();
         return $this->runAtDates;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextExecutionTime() {
        $nextCronDate = $this->cron->getNextRunDate();
        $nextRunAtDates = $this->getValidRunAtDates();
       
        $runDates = array_push($nextRunAtDates, $nextCronDate);
        
        $earliestRunDate = min($runDates);
        return $earliestRunDate->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function allowedToBeRunning() {
        
        return $this->cron->isDue() &&
                $this->isConstraintsValidated() &&
                $this->runAtTimeIsDue();       
    }

    private function runAtTimeIsDue() : bool{
        
        $nextRunAtDates = $this->getValidRunAtDates();
        $minRunatDate = min($nextRunAtDates);
        
        return $minRunatDate->getTimestamp() - time() <= 0; 
    }
    
    private function removeStaleRunAtDates(){
        $this->runAtDates = $this->removePastDates($this->runAtDates, $this->lastExecutionDate);
    }
    
    private function removePastDates($dates, $thresholdDate){
     
        return array_filter($dates, 
                function($date){
                    return $date <= $thresholdDate;                   
        });
    }
    
    private function isConstraintsValidated(){
        
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
    
    public function getStatus(){
        
    }
       
}
    