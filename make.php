<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Delegator\Build\Console\Command\BuildCommand;
use Delegator\Build\Console\Command\CheckCommand;
use Delegator\Build\Console\Command\CleanCommand;

$package = simplexml_load_file('package.template.xml');
$application = new Application();
$application->setName('Delegator - Build extension make tool');
$application->setVersion($package->version);
$application->add(new BuildCommand);
$application->add(new CheckCommand);
$application->add(new CleanCommand);
$application->run();
