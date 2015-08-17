<?php
require __DIR__ . '/__boot.php';
require __DIR__ . '/__autoload.php';

$prime = new \EasyBib\OPcache\Juggler($_POST['path-base']);
$status = $prime->recycle($_POST['path-new'], $_POST['path-old']);
exit($status);

