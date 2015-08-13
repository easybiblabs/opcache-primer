#!/usr/bin/env php
<?php
$autoloader = [
    dirname(__DIR__) . '/vendor/autoload.php', // stand-alone or in bin/
    dirname(__DIR__) . '/autoload.php', // vendor/bin
    dirname(__DIR__) . '/../../autoload.php', // dep of a dep
];

foreach ($autoloader as $autoload) {
    if (!file_exists($autoload)) {
        continue;
    }

    require $autoload;
}

use EasyBib\OPcache\Command;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Command\VarCache());
$application->add(new Command\Juggle());
$application->add(new Command\Status());
exit($application->run());
