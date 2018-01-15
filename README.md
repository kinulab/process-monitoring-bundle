# Monitoring process Bundle

[![Build Status](https://travis-ci.org/kinulab/process-monitoring-bundle.svg?branch=master)](https://travis-ci.org/kinulab/process-monitoring-bundle)

This bundle try to solve the following problem

```
How to be sure that a given service is running from 7am to 7pm ?
If the service fall it must be restarted. And how do be sure that
a service is NOT running from 7pm to 7am ? If the service is
running it must be stopped.
```

We can do it with cron but that need a few lines :

* to start the process,
* stop it at the end of day,
* check every minute from 7am to 7pm that the process is running,
* then check every minutes from 7pm to 7am that the process is
not running.

If there is only one process like that, it's ok. But when there is
more, it become hard to maintain.


## Installation


```sh
composer require kinulab/process-monitoring-bundle:^1.0
```

add in your `app/AppKernel.php` :

```php
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Kinulab\ProcessMonitoringBundle\KinulabProcessMonitoringBundle(),
        ];
    }
```

## How to use it ?

Register a service that implements the `Kinulab\ProcessMonitoringBundle\Process\ProcessDescriptorInterface`.

Tag this service as `monitor.process`.

Then ensure yourself that the `bin/console monitor:process` command
is constantly running. For that, you can use cron, monit, supervisord
or whatever you prefer to use.
