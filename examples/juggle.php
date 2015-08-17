<?php
/**
 * This could be called by bin/opcache-primer.php
 */
$autoloader = [
    dirname(__DIR__) . '/vendor/autoload.php',
    dirname(__DIR__) . '/../../autoload.php',
];

foreach ($autoloader as $autoload) {
    if (!file_exists($autoload)) {
        continue;
    }
    require $autoload;
}

$prime = new \EasyBib\OPcache\Juggler('/vagrant');
$status = $prime->recycle($_POST['new'], $_POST['old']);
exit($status);

