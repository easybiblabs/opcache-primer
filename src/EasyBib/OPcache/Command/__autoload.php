<?php
$rootDir = dirname(dirname(dirname(dirname(__DIR__))));

$autoloader = [
    $rootDir . '/vendor/autoload.php',
    $rootDir . '/../../autoload.php',
];

foreach ($autoloader as $autoload) {
    if (!file_exists($autoload)) {
        continue;
    }
    require $autoload;
}
