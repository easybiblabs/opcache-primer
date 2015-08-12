<?php
if ('fpm-fcgi' !== PHP_SAPI) {
    die('WRONG SAPI.');
}

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

$prime = new \EasyBib\OPcache\Juggler($_POST['path-base']);
$status = $prime->recycle($_POST['path-new'], $_POST['path-old']);
exit($status);

