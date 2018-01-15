<?php

namespace Kinulab\ProcessMonitoringBundle\Tests\Process;

use Kinulab\ProcessMonitoringBundle\Process\ProcessDescriptor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ProcessDescriptorTest extends TestCase
{

    public function testGettersSetters(){
        $symfonyProcess = $this->createMock(Process::class);

        $process = new ProcessDescriptor('test', $symfonyProcess);
        $process->setExplicitStart(true);
        $process->setExplicitStop(true);
        $process->setCheckInterval(10);

        $this->assertSame('test', $process->getName());
        $this->assertSame($symfonyProcess, $process->getProcess());
        $this->assertSame(true, $process->getExplicitStart());
        $this->assertSame(true, $process->getExplicitStop());
        $this->assertSame(10, $process->getCheckInterval());
    }

}
