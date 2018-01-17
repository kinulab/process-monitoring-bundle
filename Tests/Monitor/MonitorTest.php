<?php

namespace Kinulab\ProcessMonitoringBundle\Tests\Monitor;

use Kinulab\ProcessMonitoringBundle\Monitor\Monitor;
use Kinulab\ProcessMonitoringBundle\Service\ServiceDescriptor;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MonitorTest extends TestCase
{

    protected $logger;
    protected $eventDispatcher;

    public function setUp(){
        $this->logger = $this->createMock(Logger::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
    }

    /**
     * Test that monitor don't start if empty
     */
    public function testIfNoServiceRegistered(){
        $monitor = new Monitor([], $this->logger, $this->eventDispatcher);

        $this->expectException(\Exception::class);
        $monitor->startMonitoring();
    }

    /**
     * Test that only valid services are present
     */
    public function testServicesAreServiceDescriptorInterface(){
        $monitor = new Monitor([new \stdClass()], $this->logger, $this->eventDispatcher);

        $this->expectException(\Exception::class);
        $monitor->startMonitoring();
    }

    /**
     * Test that a service with explicit start is started when necessary
     */
    public function testExplicitStart(){

        // a service that must be started
        $serviceToStart = $this->createMock(ServiceDescriptor::class);
        $serviceToStart
                ->expects($this->atLeastOnce())
                ->method('getExplicitStart')
                ->willReturn(true);
        $serviceToStart
                ->expects($this->atLeastOnce())
                ->method('allowedToBeRunning')
                ->willReturn(true);
        $serviceToStart
                ->expects($this->atLeastOnce())
                ->method('isRunning')
                ->willReturn(false);
        $serviceToStart
                ->expects($this->once())
                ->method('start');

        // a service with explicit start but that sould remain stopped
        $serviceNotToStart = $this->createMock(ServiceDescriptor::class);
        $serviceNotToStart
                ->expects($this->atLeastOnce())
                ->method('getExplicitStart')
                ->willReturn(true);
        $serviceNotToStart
                ->expects($this->atLeastOnce())
                ->method('allowedToBeRunning')
                ->willReturn(false);
        $serviceNotToStart
                ->expects($this->any())
                ->method('isRunning')
                ->willReturn(false);
        $serviceNotToStart
                ->expects($this->never())
                ->method('start');

        $monitor = new Monitor([$serviceToStart, $serviceNotToStart], $this->logger, $this->eventDispatcher);


        $reflection = new \ReflectionObject($monitor);
        $startPanification = $reflection->getMethod('startPanification')->getClosure($monitor);
        $check = $reflection->getMethod('check')->getClosure($monitor);

        $startPanification();
        $check();
    }


    /**
     * Test that a service with explicit stop is stopped when necessary
     */
    public function testExplicitStop(){

        // a service that must be stopped
        $serviceToStop = $this->createMock(ServiceDescriptor::class);
        $serviceToStop
                ->expects($this->atLeastOnce())
                ->method('getExplicitStop')
                ->willReturn(true);
        $serviceToStop
                ->expects($this->atLeastOnce())
                ->method('allowedToBeRunning')
                ->willReturn(false);
        $serviceToStop
                ->expects($this->atLeastOnce())
                ->method('isRunning')
                ->willReturn(true);
        $serviceToStop
                ->expects($this->once())
                ->method('stop');

        // a service with explicit stop that should keep running
        $serviceNotToStop = $this->createMock(ServiceDescriptor::class);
        $serviceNotToStop
                ->expects($this->atLeastOnce())
                ->method('getExplicitStop')
                ->willReturn(true);
        $serviceNotToStop
                ->expects($this->atLeastOnce())
                ->method('allowedToBeRunning')
                ->willReturn(true);
        $serviceNotToStop
                ->expects($this->any())
                ->method('isRunning')
                ->willReturn(true);
        $serviceNotToStop
                ->expects($this->never())
                ->method('stop');

        $monitor = new Monitor([$serviceToStop, $serviceNotToStop], $this->logger, $this->eventDispatcher);


        $reflection = new \ReflectionObject($monitor);
        $startPanification = $reflection->getMethod('startPanification')->getClosure($monitor);
        $check = $reflection->getMethod('check')->getClosure($monitor);

        $startPanification();
        $check();
    }

    /**
     * Test that services are stopped on SIGTERM
     */
    public function testStoppingServices(){
        $service = $this->createMock(ServiceDescriptor::class);
        $service
                ->expects($this->atLeastOnce())
                ->method('isRunning')
                ->willReturn(true);
        $service
                ->expects($this->once())
                ->method('stop');

        $monitor = new Monitor([$service], $this->logger, $this->eventDispatcher);

        $reflection = new \ReflectionObject($monitor);
        $stopServices = $reflection->getMethod('stopServices')->getClosure($monitor);

        $stopServices(SIGTERM);
    }

}
